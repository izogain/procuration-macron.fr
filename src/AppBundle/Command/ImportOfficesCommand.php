<?php

namespace AppBundle\Command;

use AppBundle\Entity\Office;
use AppBundle\Mediator\AddressMediator;
use Behat\Transliterator\Transliterator;
use EnMarche\Bundle\CoreBundle\Intl\FranceCitiesBundle;
use League\Csv\Reader;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOfficesCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('procuration:offices:import')
            ->setDescription('Import offices from dumped CSV file')
            ->addArgument('file-path', InputArgument::OPTIONAL, 'The absolute file path to use for import')
            ->addOption('batch-size', 'b', InputOption::VALUE_OPTIONAL, 'The persistence batch size', 5000);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$filePath = $input->getArgument('file-path')) {
            $filePath = $this->getContainer()->getParameter('offices_csv_file_path');
        }

        try {
            $reader = Reader::createFromPath($filePath);
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e));

            return 1;
        }

        $reader->setDelimiter(';');
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $batchSize = (int) $input->getOption('batch-size');
        $streetTypes = array_map('mb_strtolower', AddressMediator::getStreetTypes());

        // Reporting data
        $duplicateData = $invalidRowsFormat = $missingPostalCodes = $mismatchInseeCityCodes = [];
        $processedRowCount = 0;
        $cities = FranceCitiesBundle::$cities;

        foreach ($reader as $i => $row) {
            $rowSize = count($row);

            // The rows with no street name is a 5 items length array
            if (5 == $rowSize) {
                list($name, $postalCode, $city, $openingHour, $closingHour) = $row;
                $streetName = null;
            } elseif (6 == $rowSize) {
                list($name, $streetName, $postalCode, $city, $openingHour, $closingHour) = $row;
            } else {
                $invalidRowsFormat[$i + 1] = $row;
                continue;
            }

            $office = new Office();
            $office->setName(trim($name));

            $officeAddress = $office->getAddress();
            $officeAddress->setPostalCode(trim($postalCode));
            $officeAddress->setCityName($city);

            // The same office has already been registered
            if (isset($duplicateData[$officeAddress->getPostalCode()][$office->getName()])) {
                ++$duplicateData[$officeAddress->getPostalCode()][$office->getName()];

                continue;
            }

            // The city INSEE code is not defined
            if (!isset($cities[$officeAddress->getPostalCode()])) {
                if (!isset($missingPostalCodes[$officeAddress->getPostalCode()])) {
                    $missingPostalCodes[$officeAddress->getPostalCode()] = [];
                }

                $missingPostalCodes[$officeAddress->getPostalCode()][] = $i + 1;

                continue;
            }

            $cityNames = array_map('\Behat\Transliterator\Transliterator::urlize', $cities[$officeAddress->getPostalCode()]);

            if (false === ($inseeCityCode = array_search(Transliterator::urlize($officeAddress->getCityName()), $cityNames))) {
                if (!isset($mismatchInseeCityCodes[$officeAddress->getPostalCode()][$officeAddress->getCityName()])) {
                    $mismatchInseeCityCodes[$officeAddress->getPostalCode()][$officeAddress->getCityName()] = [];
                }

                $mismatchInseeCityCodes[$officeAddress->getPostalCode()][$officeAddress->getCityName()][] = $i + 1;

                continue;
            }

            $office->setRegularOpeningHour((int) $openingHour);
            $office->setRegularClosingHour((int) $closingHour);

            if ($streetName) {
                preg_match('/([\d]+)?([,|\s]+)?(.*)/i', $streetName, $matches);

                if ($matches[1]) {
                    $officeAddress->setStreetNumber((int) $matches[1]);
                }

                if ($matches[3]) {
                    $streetName = trim($matches[3]);
                    $streetTypeAttempt = explode(' ', $streetName);

                    // Street has a known type
                    if (false !== $streetType = array_search(mb_strtolower($streetTypeAttempt[0]), $streetTypes)) {
                        $officeAddress->setStreetType($streetType);
                        array_shift($streetTypeAttempt);

                        $officeAddress->setStreetName(trim(implode(' ', $streetTypeAttempt)));
                    } else {
                        $officeAddress->setStreetName(trim($streetName));
                    }
                }
            }

            ++$processedRowCount;
            $officeAddress->setInseeCityCode($inseeCityCode);
            $officeAddress->setCountryCode('FR');

            $entityManager->persist($office);

            $duplicateData[$officeAddress->getPostalCode()][$office->getName()] = 1;

            if ($processedRowCount % $batchSize == 0) {
                $entityManager->flush();
                $this->writeProgressInformation($output, $processedRowCount);
            }
        }

        $entityManager->flush();

        $this->displayDuplicatedRows($output, $duplicateData);
        $this->displayInvalidRowsFormat($output, $invalidRowsFormat);
        $this->displayMissingPostalCodes($output, $missingPostalCodes);
        $this->displayMismatchInseeCodes($output, $mismatchInseeCityCodes);

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param int             $i
     */
    private function writeProgressInformation(OutputInterface $output, $i)
    {
        $output->writeln(sprintf('<info>Commited %d rows</info>', $i));
        $output->writeln(sprintf('%dMo peak consumption', round(memory_get_peak_usage() / 1024 / 1024)));
    }

    /**
     * Display some stats about duplicated informations.
     *
     * @param OutputInterface $output
     * @param array           $duplicateData
     */
    private function displayDuplicatedRows(OutputInterface $output, array $duplicateData)
    {
        $flattenedData = [];

        foreach ($duplicateData as $postalCode => $officeInfos) {
            foreach ($officeInfos as $officeName => $nbRepeat) {
                if (1 == $nbRepeat) {
                    continue;
                }

                $flattenedData[] = [
                    'postal_code' => $postalCode,
                    'office_name' => $officeName,
                    'found' => $nbRepeat,
                ];
            }
        }

        $nbDuplicates = count($flattenedData);

        if (!$nbDuplicates) {
            return;
        }

        $output->writeln(str_repeat("\n", 4));
        $output->writeln(sprintf('<error>%d duplicated lines</error>', $nbDuplicates));

        foreach ($flattenedData as $dataInfos) {
            $output->writeln(sprintf('%s - %s: found %d time(s)', $dataInfos['postal_code'], $dataInfos['office_name'], $dataInfos['found']));
        }
    }

    /**
     * Display the number of not properly formatted rows.
     *
     * @param OutputInterface $output
     * @param array           $invalidRowsFormat
     */
    private function displayInvalidRowsFormat(OutputInterface $output, array $invalidRowsFormat)
    {
        $nbInvalidRows = count($invalidRowsFormat);

        if (!$nbInvalidRows) {
            return;
        }

        $output->writeln(str_repeat("\n", 4));
        $output->writeln(sprintf('<error>%d invalid rows:</error>', $nbInvalidRows));

        foreach ($invalidRowsFormat as $lineNumber => $data) {
            $output->writeln(sprintf('Line %d: %s', $lineNumber, json_encode($data)));
        }
    }

    /**
     * Display the different missing INSEE codes.
     *
     * @param OutputInterface $output
     * @param array           $missingInseeCodes
     */
    private function displayMissingPostalCodes(OutputInterface $output, array $missingInseeCodes)
    {
        $nbMissingInseeCodes = count($missingInseeCodes);

        if (!$nbMissingInseeCodes) {
            return;
        }

        $output->writeln(str_repeat("\n", 4));
        $output->writeln(sprintf('<error>%d missing postal codes:</error>', $nbMissingInseeCodes));

        foreach ($missingInseeCodes as $postalCode => $rows) {
            $output->writeln(sprintf('<info>%s</info>: at lines %s', $postalCode, implode(', ', $rows)));
        }
    }

    /**
     * @param OutputInterface $output
     * @param array           $mismatchInseeCityCodes
     */
    private function displayMismatchInseeCodes(OutputInterface $output, array $mismatchInseeCityCodes)
    {
        $nbMismatchInseeCodes = count($mismatchInseeCityCodes);

        if (!$nbMismatchInseeCodes) {
            return;
        }

        $output->writeln(str_repeat("\n", 4));
        $output->writeln(sprintf('<error>%s mismatching INSEE codes:</error>', $nbMismatchInseeCodes));

        foreach ($mismatchInseeCityCodes as $postalCode => $cities) {
            $output->writeln(sprintf('<info>For postal code %s:</info>', $postalCode));

            foreach ($cities as $cityName => $rows) {
                $output->writeln(sprintf('%s at rows: %s', $cityName, implode(', ', $rows)));
            }
        }
    }
}
