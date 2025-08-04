<?php
class Conn
{
    private $host = "127.0.0.1";
    private $dbname = "SGWA"; 
    private $usuario = "root";
    private $senha = "1234";
    private $porta = "3306";
    private $conexao;

    public function __construct()
    {
        $this->conectar();
    }

    private function conectar() 
    {
        try {
            $this->conexao = new PDO(
                "mysql:host={$this->host};port={$this->porta};dbname={$this->dbname};charset=utf8",
                $this->usuario,
                $this->senha
            );
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage()); // Se der erro, encerra o script
        }
    }

    public function getConexao()
    {
        if ($this->conexao === null) { // Se a conexão não existir, tenta reconectar
            $this->conectar();
        }
        return $this->conexao;
    }
}
?>
