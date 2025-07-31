<?php
class acessoDAO
{
    private $conexao;

    public function __construct()
    {
        $conexaoObj = new Conn();
        $this->conexao = $conexaoObj->getConexao();

        if ($this->conexao === null) {
            die("Erro: Falha ao obter a conexão com o banco de dados.");
        }
    }


    //Autenticar o Acesso
public function autenticar_acesso($n_acesso)
    {
        try {
            $sql = "SELECT * FROM acesso WHERE acesso = :acesso";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":acesso", $n_acesso);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }
}

?>