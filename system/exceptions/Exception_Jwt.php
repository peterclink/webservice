<?php
//Classe apenas para identificar o tipo do erro
class Exception_Jwt extends Exception {
    //Recebe obrigatoriamente a mensagem, e o codigo pode ser omitido
    public function __construct($mensagem,$codigo = 0) { 
        parent::__construct($mensagem,$codigo);
    }
}
?>