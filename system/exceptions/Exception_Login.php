<?php
//Classe apenas para identificar o tipo do erro
class Exception_Login extends Exception {
    //Recebe obrigatoriamente a mensagem, e o codigo pode ser omitido
    public function __construct($mensagem = null,$codigo = 0) { 
        parent::__construct($mensagem,$codigo);
    }
}
?>