<?php
class LoginHelper extends LoginModel {
    
    private $iniciaSessao = true;
    private $prefixoChaves = 'usuario_';
    private $cookie = true;
    private $cookieTime = 7;
    private $cookiePath = '/';
    public $erro = '';

    public function login($login,$senha,$lembrar = false) {
        
        if ( $this->validarUsuario($login,$senha) ) {
            
            $this->setSession();
            $this->sessionStart();
            $this->setDataSession();
            $this->setCookie();

            if ($lembrar) 
                $this->lembrarDados();

            return true;

        } else {
            $this->erro = 'Usuário inválido';
            return false;
        }
    }  

    public function sessionStart() {
        if ($this->iniciaSessao AND !isset($_SESSION)) {
            session_start();
        }
    }

    public function setSession() {
        
        if ($this->iniciaSessao AND !isset($_SESSION)) {
            session_cache_limiter('private');
            session_cache_expire(30);
        }
    }

    public function setDataSession() {
        
        $dados = $this->getData();

        $i= 0;
        foreach ($dados as $key) {
            foreach ($key as $value) {
                $_SESSION[$this->prefixoChaves . $this->_dados[$i]] = $value;
                $i++;
            }
        }
        $_SESSION[$this->prefixoChaves . 'logado'] = true;
    }

    public function getSessionExpire() {
        var_dump($_SESSION);
        $this->sessionStart();
        $cache_limiter = session_cache_limiter();
        $cache_expire = session_cache_expire();
        echo "O limitador de cache esta definido agora como $cache_limiter<br />"; 
        echo "As sessões em cache irão expirar em $cache_expire minutos";
    }

    public function setCookie() {
        // Define um cookie para maior segurança?
        if ($this->cookie) {
            // Monta uma cookie com informações gerais sobre o usuário: usuario, ip e navegador
            $valor = join('#', array($this->_login, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));
            
            // Encripta o valor do cookie
            $valor = sha1($valor);
            
            // Cria o cookie
            setcookie($this->prefixoChaves . 'token', $valor, 0, $this->cookiePath);
        }
    }

    public function isLogged($cookies = true) {

        $this->sessionStart();

        if (!isset($_SESSION[$this->prefixoChaves . 'logado']) OR !$_SESSION[$this->prefixoChaves . 'logado']) {
            // Verifica os dados salvos nos cookies?
            if ($cookies) {
                // Se os dados forem válidos o usuário é logado automaticamente
                return $this->checkRememberData();
            } else {
                // Não há usuário logado
                $this->erro = 'Não há usuário logado';
                return false;
            }
        }

        if ($this->cookie) {
            // Verifica se o cookie não existe
            if (!isset($_COOKIE[$this->prefixoChaves . 'token'])) {
                $this->erro = 'Não há usuário logado';
                return false;
            } else {
                // Monta o valor do cookie
                $valor = join('#', array($_SESSION[$this->prefixoChaves . 'login'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));
    
                // Encripta o valor do cookie
                $valor = sha1($valor);
    
                // Verifica o valor do cookie
                if ($_COOKIE[$this->prefixoChaves . 'token'] !== $valor) {
                    $this->erro = 'Não há usuário logado';
                    return false;
                }
            }
        }

        return true;
    }

    public function logout($cookies = true) {

        $this->sessionStart();

        // Tamanho do prefixo
        $tamanho = strlen($this->prefixoChaves);

        // Destroi todos os valores da sessão relativos ao sistema de login
        foreach ($_SESSION AS $chave=>$valor) {
            // Remove apenas valores cujas chaves comecem com o prefixo correto
            if (substr($chave, 0, $tamanho) == $this->prefixoChaves) {
                unset($_SESSION[$chave]);
            }
        }
        
        // Destrói a sessão se ela estiver vazia
        if (count($_SESSION) == 0) {
            session_destroy();
            
            // Remove o cookie da sessão se ele existir
            if (isset($_COOKIE['PHPSESSID'])) {
                setcookie('PHPSESSID', false, (time() - 3600));
                unset($_COOKIE['PHPSESSID']);
            }
        }
        
        // Remove o cookie com as informações do visitante
        if ($this->cookie AND isset($_COOKIE[$this->prefixoChaves . 'token'])) {
            setcookie($this->prefixoChaves . 'token', false, (time() - 3600), $this->cookiePath);
            unset($_COOKIE[$this->prefixoChaves . 'token']);
        }
        
        // Limpa também os cookies de "Lembrar minha senha"?
        if ($cookies) $this->clearRememberData();
        
        // Retorna SE não há um usuário logado (sem verificar os cookies)
        return !$this->isLogged(false);
    }
    
    public function rememberData() {    
        // Calcula o timestamp final para os cookies expirarem
        $tempo = strtotime("+{$this->cookieTime} day", time());

        // Encripta os dados do usuário usando base64
        // O rand(1, 9) cria um digito no início da string que impede a descriptografia
        $usuario = rand(1, 9) . base64_encode($this->_login);
        $senha = rand(1, 9) . base64_encode($this->_pass);
    
        // Cria um cookie com o usuário
        setcookie($this->prefixoChaves . 'lu', $usuario, $tempo, $this->cookiePath);
        // Cria um cookie com a senha
        setcookie($this->prefixoChaves . 'ls', $senha, $tempo, $this->cookiePath);
    }
    
    public function checkRememberData() {
        // Os cookies de "Lembrar minha senha" existem?
        if (isset($_COOKIE[$this->prefixoChaves . 'lu']) AND isset($_COOKIE[$this->prefixoChaves . 'ls'])) {
            // Pega os valores salvos nos cookies removendo o digito e desencriptando
            $usuario = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'lu'], 1));
            $senha = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'ls'], 1));
            
            // Tenta logar o usuário com os dados encontrados nos cookies
            return $this->login($usuario, $senha, true);        
        }
        
        // Não há nenhum cookie, dados inválidos
        return false;
    }
    
    public function clearRememberData() {
        // Deleta o cookie com o usuário
        if (isset($_COOKIE[$this->prefixoChaves . 'lu'])) {
            setcookie($this->prefixoChaves . 'lu', false, (time() - 3600), $this->cookiePath);
            unset($_COOKIE[$this->prefixoChaves . 'lu']);            
        }
        // Deleta o cookie com a senha
        if (isset($_COOKIE[$this->prefixoChaves . 'ls'])) {
            setcookie($this->prefixoChaves . 'ls', false, (time() - 3600), $this->cookiePath);
            unset($_COOKIE[$this->prefixoChaves . 'ls']);            
        }
    }
}