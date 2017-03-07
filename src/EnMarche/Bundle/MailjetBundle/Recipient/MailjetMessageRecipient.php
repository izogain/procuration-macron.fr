<?php

namespace EnMarche\Bundle\MailjetBundle\Recipient;

final class MailjetMessageRecipient
{
    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var null|string
     */
    private $fullName;

    /**
     * @var array
     */
    private $vars;

    /**
     * @param string      $emailAddress
     * @param string|null $fullName
     * @param array       $vars
     */
    public function __construct(string $emailAddress, string $fullName = null, array $vars = [])
    {
        $this->emailAddress = $emailAddress;
        $this->fullName = $fullName;
        $this->vars = $vars;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @return null|string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }
}
