<?php

namespace AppBundle\FPDI;

use AppBundle\Entity\Address;
use AppBundle\Entity\ElectionRound;
use AppBundle\Entity\Procuration;
use AppBundle\Mediator\AddressMediator;
use AppBundle\Mediator\ProcurationMediator;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Intl\Intl;

class FPDIWriter
{
    /**
     * @var string
     */
    protected $sourceFile;

    /**
     * @var ProcurationMediator
     */
    protected $procurationMediator;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @param string              $sourceFile
     * @param ProcurationMediator $procurationMediator
     * @param Filesystem          $filesystem
     */
    public function __construct($sourceFile, ProcurationMediator $procurationMediator, Filesystem $filesystem)
    {
        $this->sourceFile = $sourceFile;
        $this->procurationMediator = $procurationMediator;
        $this->fileSystem = $filesystem;
    }

    /**
     * @param Procuration $procuration
     */
    public function generateForProcuration(Procuration $procuration)
    {
        $requester = $procuration->getRequester();
        $requesterVotingOffice = $requester->getVotingOffice();
        $requesterVotingOfficeAddress = $requesterVotingOffice->getAddress();
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $pdf = new \FPDI();

        // Create the first page in landscape format
        $pdf->addPage('L', 'A4');
        $pdf->setSourceFile($this->sourceFile);
        $templateFirstPage = $pdf->importPage(1);
        $pdf->useTemplate($templateFirstPage);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        $requesterLastName = $this->formatLastName($requester->getLastName());
        $requesterFirstName = $this->formatFirstName($requester->getFirstName());

        $this->writeTextAtPos($pdf, $requesterLastName, 46, 18);
        $this->writeTextAtPos($pdf, $requesterFirstName, 34, 21.5);

        // Address
        $requesterAddress = $requester->getAddress();
        $this->writeFirstAddressLine($pdf, $requesterAddress, 47, 25.2);
        $this->writeSecondAddressLine($pdf, $requesterAddress, 34.5, 33.5);
        $this->writeTextAtPos($pdf, $phoneNumberUtil->format($requester->getPhoneNumber(), PhoneNumberFormat::E164), 22, 38.5);
        $this->writeTextAtPos($pdf, $requester->getEmail(), 78, 38.5);

        $this->writeTextInBoxes($pdf, $this->formatDate($requester->getBirthDate()), 31, 44.5);

        // Depending of the user country we have to check a different box and provide different informations
        if ('FR' == $requesterVotingOfficeAddress->getCountryCode()) {
            $this->checkABox($pdf, 17.4, 53.5);
            $this->writeTextAtPos($pdf, $requesterVotingOfficeAddress->getCityName(), 54, 56);
        } else {
            $this->checkABox($pdf, 17.2, 63);
            $this->writeTextAtPos($pdf, Intl::getRegionBundle()->getCountryName($requesterVotingOfficeAddress->getCountryCode()), 32, 70.5);
        }

        // Voter informations
        $voter = $procuration->getVotingAvailability()->getVoter();
        $voterAddress = $voter->getAddress();
        $voterLastName = $this->formatLastName($voter->getLastName());
        $voterFirstName = $this->formatFirstName($voter->getFirstName());

        $this->checkABox($pdf, 20.5, 81.5);
        $this->writeTextAtPos($pdf, $voterLastName, 50, 89);
        $this->writeTextAtPos($pdf, $voterFirstName, 37, 93);
        $this->writeFirstAddressLine($pdf, $voterAddress, 52, 96.5);
        $this->writeSecondAddressLine($pdf, $voterAddress, 38.5, 104.5);
        $this->writeTextInBoxes($pdf, $this->formatDate($voter->getBirthDate()), 35, 111);

        // Election informations
        $electionRound = $procuration->getElectionRound();
        $electionDate = $this->formatDate($electionRound->getPerformanceDate());
        $electionTypeName = $electionRound->getElection()->getLabel();
        $this->checkABox($pdf, 19.7, 129.3, 3.7);
        $this->writeTextAtPos($pdf, $electionTypeName, 48, 131);

        if ($this->isFirstRound($electionRound)) {
            $this->checkABox($pdf, 25, 137, 2.5);
        } else {
            $this->checkABox($pdf, 25, 141, 2.5);
        }

        $this->writeTextInBoxes($pdf, $electionDate, 95, 131);

        // Conformity part
        $currentDate = new \DateTime();
        $this->writeTextAtPos($pdf, $requesterAddress->getCityName(), 30, 169.5);
        $this->writeTextInBoxes($pdf, $this->formatDate($currentDate), 26.5, 174.5);
        $this->writeTextInBoxes($pdf, $currentDate->format('H'), 30.5, 179.5);
        $this->writeTextInBoxes($pdf, $currentDate->format('i'), 43, 179.5);

        // Create the second page in portrait format
        $pdf->addPage('P', 'A4');
        $pdf->setSourceFile($this->sourceFile);
        $templateSecondPage = $pdf->importPage(2);
        $pdf->useTemplate($templateSecondPage);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        $this->writeTextAtPos($pdf, $requesterLastName, 88, 32.5);
        $this->writeTextAtPos($pdf, $requesterFirstName, 76, 38.5);
        $this->checkReasonBox($pdf, $procuration);
        $this->writeTextInBoxes($pdf, $this->formatDate($currentDate), 68, 116);

        $this->writeTextAtPos($pdf, $requesterLastName, 88, 164);
        $this->writeTextAtPos($pdf, $requesterFirstName, 76, 171);
        $this->checkABox($pdf, 62.5, 188);

        $this->writeTextAtPos($pdf, $voterLastName, 91.5, 195.5);
        $this->writeTextAtPos($pdf, $voterFirstName, 80, 201.5);
        $this->checkABox($pdf, 62, 205.5, 3.5);
        $this->writeTextAtPos($pdf, $electionTypeName, 88, 207.5);
        $this->writeTextInBoxes($pdf, $electionDate, 115, 207.5);

        if ($this->isFirstRound($electionRound)) {
            $this->checkABox($pdf, 65, 216.5, 2.5);
        } else {
            $this->checkABox($pdf, 65, 221.5, 2.5);
        }

        $this->writeTextInBoxes($pdf, $this->formatDate($currentDate), 71.5, 251);
        $this->writeTextInBoxes($pdf, $currentDate->format('H'), 118, 251);
        $this->writeTextInBoxes($pdf, $currentDate->format('i'), 129.5, 251);

        $this->writeTextAtPos($pdf, $requesterAddress->getCityName(), 112.5, 258);

        $outputFilePath = $this->procurationMediator->generateOutputFilePath($procuration);
        $this->createOutputDir($outputFilePath);
        $pdf->Output('F', $outputFilePath);
    }

    /**
     * @param string $outputFilePath
     */
    protected function createOutputDir($outputFilePath)
    {
        $this->fileSystem->mkdir([dirname($outputFilePath)]);
    }

    /**
     * Ease text writing into boxes because that should be readable in such cases as postal code, birthdate and so on.
     *
     * @param \FPDI     $pdf
     * @param string    $content
     * @param int|float $x       The starting X position in mms
     * @param int|float $y       The starting Y position in mms
     * @param int       $boxStep The box width
     */
    private function writeTextInBoxes(\FPDI $pdf, $content, $x, $y, $boxStep = 4)
    {
        $size = mb_strlen($content);

        for ($i = 0; $i < $size; ++$i) {
            $this->writeTextAtPos($pdf, $content[$i], $x + $i * $boxStep, $y);
        }
    }

    /**
     * Easily generate a checkbox.
     *
     * @param \FPDI     $pdf
     * @param int|float $x
     * @param int|float $y
     * @param int|float $squareSize
     */
    private function checkABox(\FPDI $pdf, $x, $y, $squareSize = 4)
    {
        $pdf->Rect($x, $y, $squareSize, $squareSize, 'F');
    }

    /**
     * Write the full first address line. The position of the fields is relative to $x.
     *
     * @param \FPDI     $pdf
     * @param Address   $address
     * @param int|float $x       The X axis start position
     * @param int|float $y       The Y axis start position
     */
    private function writeFirstAddressLine(\FPDI $pdf, Address $address, $x, $y)
    {
        if ($streetNumber = $address->getStreetNumber()) {
            $this->writeTextAtPos($pdf, $streetNumber, $x, $y);
        }

        if (null !== $streetRepeater = $address->getStreetRepeater()) {
            $this->writeTextAtPos($pdf, AddressMediator::getStreetRepeaterLabel($streetRepeater), $x + 6.5, $y);
        }

        if (null !== $streetType = $address->getStreetType()) {
            $this->writeTextAtPos($pdf, AddressMediator::getStreetTypeLabel($streetType), $x + 22.5, $y);
        }

        // Max size should be 34 chars
        $this->writeTextAtPos($pdf, $address->getStreetName(), $x + 35.5, $y);
    }

    /**
     * Write the full second address line. The position of the fields is relative to $x.
     *
     * @param \FPDI     $pdf
     * @param Address   $address
     * @param int|float $x       The X axis start position
     * @param int|float $y       The Y axis start position
     */
    private function writeSecondAddressLine(\FPDI $pdf, Address $address, $x, $y)
    {
        $this->writeTextInBoxes($pdf, $address->getPostalCode(), $x, $y);
        $this->writeTextAtPos($pdf, $address->getCityName(), $x + 40, $y);
    }

    /**
     * @param \FPDI     $pdf
     * @param string    $content
     * @param int|float $x       The X axis positions in mms
     * @param int|float $y       The Y axis positions in mms
     */
    private function writeTextAtPos(\FPDI $pdf, $content, $x, $y)
    {
        $pdf->SetXY($x, $y);
        $pdf->Write(0, utf8_decode($content));
    }

    /**
     * @param \FPDI       $pdf
     * @param Procuration $procuration
     */
    private function checkReasonBox(\FPDI $pdf, Procuration $procuration)
    {
        $y = 63.4;

        switch ($procuration->getReason()) {
            case ProcurationMediator::REASON_HANDICAP:
                $y += 5.8;
                break;
            case ProcurationMediator::REASON_HEALTH:
                $y += 11.5;
                break;
            case ProcurationMediator::REASON_REQUIRES_ASSISTANCE:
                $y += 17.3;
                break;
            case ProcurationMediator::REASON_FORMATION:
                $y += 27.1;
                break;
            case ProcurationMediator::REASON_HOLIDAYS:
                $y += 33.2;
                break;
            case ProcurationMediator::REASON_OTHER_LIVING_PLACE:
                $y += 38.9;
                break;
        }

        $this->checkABox($pdf, 59.5, $y, 2.5);
    }

    /**
     * @param string $lastName
     *
     * @return string
     */
    private function formatLastName($lastName)
    {
        return mb_strtoupper($lastName);
    }

    /**
     * @param string $firstName
     *
     * @return string
     */
    private function formatFirstName($firstName)
    {
        return ucwords(mb_strtolower($firstName));
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    private function formatDate(\DateTime $dateTime)
    {
        return $dateTime->format('dmY');
    }

    /**
     * @param ElectionRound $electionRound
     *
     * @return bool
     */
    private function isFirstRound(ElectionRound $electionRound)
    {
        $possibleRounds = $electionRound->getElection()->getRounds();

        foreach ($possibleRounds as $possibleRound) {
            if ($possibleRound->getId() == $electionRound->getId()) {
                continue;
            }

            if ($possibleRound->getPerformanceDate() < $electionRound->getPerformanceDate()) {
                return false;
            }
        }

        return true;
    }
}
