<?php 
class Config{
    private $config = [];
    private static  $instance = null;
    
    private function __construct(){
        $this->config['debug'] = true;
        $this->config['development']['host'] = 'localhost';
        $this->config['development']['name'] = 'rezendeapp';
        $this->config['development']['user'] = 'root';
        $this->config['development']['pass'] = '115243';

        $this->config['production']['host'] = 'localhost';
        $this->config['production']['name'] = 'rezendeapp';
        $this->config['production']['user'] = 'root';
        $this->config['production']['pass'] = '115243';
    }

    public static function  getInstance(){
        if(self::$instance == null)
        {
            self::$instance = new Config;
            return  self::$instance;
        }
        
        return self::$instance;
    }

    public function getConfiguration($tipo){
        try{
            if(isset($tipo, $this->config)){
                return  $this->config[$tipo];
            }
            else{
                throw new Exception("Não foi possível acessar configuraçãoo");
            }
    
        }
		catch (Exception $e){
            throw new Exception($e.getMessage());
        }
    }
}
?>