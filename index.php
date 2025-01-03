<?php
// Configurar cabeçalhos para JSON
header('Content-Type: application/json');

// Exibir erros relevantes
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

// Diretório base
$baseDir = __DIR__ . '/';
$pasta = isset($_GET['dir']) ? $baseDir . $_GET['dir'] : $baseDir . 'ANO';

// Normalizar o caminho
$pasta = realpath($pasta);

// Validar o diretório
if ($pasta === false || strpos($pasta, realpath($baseDir)) !== 0 || !is_dir($pasta)) {
    http_response_code(400);
    echo json_encode([
        "error" => "Diretório inválido",
        "path" => $pasta,
        "baseDir" => realpath($baseDir)
    ]);
    exit;
}

// Listar pastas e arquivos
$result_json = [];
$diretorio = opendir($pasta);

while (($arquivo = readdir($diretorio)) !== false) {
    if ($arquivo != '.' && $arquivo != '..') {
        $caminhoCompleto = $pasta . DIRECTORY_SEPARATOR . $arquivo;
        $tipo = is_dir($caminhoCompleto) ? 'folder' : 'file';
        $dataModificacao = date('Y-m-d H:i:s', filemtime($caminhoCompleto));
        $tamanho = $tipo === 'file' ? filesize($caminhoCompleto) : null;

        $result_json[] = [
            'name' => $arquivo,
            'path' => $caminhoCompleto,
            'type' => $tipo,
            'last_modified' => $dataModificacao,
            'size' => $tamanho
        ];
    }
}

closedir($diretorio);

// Responder com JSON
echo json_encode($result_json, JSON_PRETTY_PRINT);
?>