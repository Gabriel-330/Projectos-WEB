<?php
require_once("conn.php");


// resto do código...


class verificarTabelaDAO
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

   public function tabelaTemDados(string $tabela): bool
{
    try {
        $sql = "SELECT COUNT(*) as total FROM {$tabela}";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    } catch (Exception $e) {
        echo "Erro ao verificar dados na tabela: " . $e->getMessage();
        return false;
    }
}

}
