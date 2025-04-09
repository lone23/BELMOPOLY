<?php
namespace models;
class Utente
{
    private $username;
    private $password;

    private $email;
    private $data_creazione;

    /**
     * @param $username
     * @param $password
     * @param $data_creazione
     * @param $email
     */



    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getDataCreazione()
    {
        return $this->data_creazione;
    }

    /**
     * @param mixed $data_creazione
     */
    public function setDataCreazione($data_creazione)
    {
        $this->data_creazione = $data_creazione;
    }







}