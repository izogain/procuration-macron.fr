<?php

namespace AppBundle\Command;

use AppBundle\Entity\Office;
use AppBundle\Mediator\AddressMediator;
use League\Csv\Reader;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOfficesCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('procuration:offices:import')
            ->setDescription('Import offices from dumped CSV file')
            ->addArgument('file-path', InputArgument::OPTIONAL, 'The absolute file path to use for import')
            ->addOption('batch-size', 'b', InputOption::VALUE_OPTIONAL, 'The persistence batch size', 5000);
    }

    /**
     * @inheritDoc
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
        $duplicateData = $invalidRowsFormat = [];
        $processedRowCount = 0;

        foreach ($reader as $i => $row) {
            $rowSize = count($row);

            // The rows with no street name is a 5 items length array
            if (5 == $rowSize) {
                list($name, $postalCode, $city, $openingHour, $closingHour) = $row;
                $streetName = '';
            } elseif (6 == $rowSize) {
                list($name, $streetName, $postalCode, $city, $openingHour, $closingHour) = $row;
            } else {
                $invalidRowsFormat[$i] = $row;
                continue;
            }

            $office = new Office();
            $office->setName(trim($name));

            $officeAddress = $office->getAddress();
            $officeAddress->setPostalCode(trim($postalCode));

            if (isset($duplicateData[$officeAddress->getPostalCode()][$office->getName()])) {
                ++$duplicateData[$officeAddress->getPostalCode()][$office->getName()];

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
            $officeAddress->setCity($city);
            $officeAddress->setCountryCode('FR');

            $entityManager->persist($office);

            $duplicateData[$officeAddress->getPostalCode()][$office->getName()] = 1;

            if ($processedRowCount%$batchSize == 0) {
                $entityManager->flush();
                $this->writeProgressInformation($output, $processedRowCount);
            }
        }

        $entityManager->flush();

        $this->displayDuplicatedRows($output, $duplicateData);
        $this->displayInvalidRowsFormat($output, $invalidRowsFormat);

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param int             $i
     */
    private function writeProgressInformation(OutputInterface $output, $i)
    {
        $output->writeln(sprintf('<info>Commited %d rows</info>', $i));
        $output->writeln(sprintf('%dMo peak consumption', round(memory_get_peak_usage()/1024/1024)));
    }

    /**
     * Display some stats about duplicated informations
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

        $output->writeln(sprintf('<error>%d duplicated lines</error>', $nbDuplicates));

        foreach ($flattenedData as $dataInfos) {
            $output->writeln(sprintf('%s - %s: found %d time(s)', $dataInfos['postal_code'], $dataInfos['office_name'], $dataInfos['found']));
        }
    }

    /**
     * Display the number of not properly formatted rows
     *
     * @param OutputInterface $output
     * @param                 $invalidRowsFormat
     */
    private function displayInvalidRowsFormat(OutputInterface $output, $invalidRowsFormat)
    {
        $nbInvalidRows = count($invalidRowsFormat);

        if (!$nbInvalidRows) {
            return;
        }

        $output->writeln(sprintf('<error>%d invalid rows:</error>', $nbInvalidRows));

        foreach ($invalidRowsFormat as $lineNumber => $data) {
            $output->writeln(sprintf('Line %d: %s', $lineNumber, json_encode($data)));
        }
    }
}
