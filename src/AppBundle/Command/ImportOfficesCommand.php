<?php

namespace AppBundle\Command;

use AppBundle\Entity\Office;
use AppBundle\Mediator\AddressMediator;
use League\Csv\Reader;
use Symfony\Component\Config\Definition\Exception\Exception;
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
            ->addOption('batch-size', 'b', InputOption::VALUE_OPTIONAL, 'The persistence batch size', 100);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $reader = Reader::createFromPath($this->getContainer()->getParameter('offices_csv_file_path'));
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e));

            return 1;
        }

        $reader->setDelimiter(';');
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $batchSize = (int) $input->getOption('batch-size');
        $streetTypes = array_map('mb_strtolower', AddressMediator::getStreetTypes());

        foreach ($reader as $i => $row) {
            list($name, $streetName, $postalCode, $city, $openingHour, $closingHour) = $row;
            $office = new Office();
            $office->setName($name);
            $office->setRegularOpeningHour((int) $openingHour);
            $office->setRegularClosingHour((int) $closingHour);

            $officeAddress = $office->getAddress();
            preg_match('/([\d]+)?([,|\s]+)?(.*)/i', $streetName, $matches);

            if ($matches[1]) {
                $officeAddress->setStreetNumber((int) $matches[1]);
            }

            if ($matches[3]) {
                $streetName = trim($matches[3]);
                $streetTypeAttempt = explode(' ', $streetName);

                // Street has a know type
                if (false !== $streetType = array_search(mb_strtolower($streetTypeAttempt[0]), $streetTypes)) {
                    $officeAddress->setStreetType($streetType);
                    array_shift($streetTypeAttempt);

                    $officeAddress->setStreetName(implode(' ', $streetTypeAttempt));
                } else {
                    $officeAddress->setStreetName($streetName);
                }
            }

            $officeAddress->setPostalCode($postalCode);
            $officeAddress->setCity($city);
            $officeAddress->setCountryCode('FR');

            $entityManager->persist($office);

            if ($i+1%$batchSize == 0) {
                $entityManager->flush();
                $this->writeProgressInformation($output, $i + 1);
            }
        }

        $entityManager->flush();

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
}
