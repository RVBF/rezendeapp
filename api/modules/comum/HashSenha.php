<?php

class HashSenha
{

    private function __construct()	{}
    private function __clone()	{}
    private function __wakeup()	{}
    private static $singleton = null;
    
    static function instance()
	{
		if (null == self::$singleton)
		{
            self::$singleton = new HashSenha;
			return self::$singleton;
        }
        
		return self::$singleton;
	}

	/**
	*  Cria uma senha criptografada em MD5
	*  @throws ColecaoException
	*  @return $senha
	*/
	function gerarHashDeSenhaComSaltEmMD5($senha)
	{
		$senhaCriptografada = '';

		$salt = 'b!cC_1#8AF6y21zBp4XV-5t^dvI_8KWmlcg-s$d9P#BDRD113344@@u2*6_U8^60a!!^J%*7%S97#i0j$fakZ57c!oq0He#xOfL9brTewh-E$$%A@2MC*%QNU2En35$YF@G8
		';
		$i = 0;

		while ($i <= 7)
		{

			$senhaCriptografada .= $salt . $senha . $salt;
			$i++;
		}

		return md5($senhaCriptografada);
	}
}
