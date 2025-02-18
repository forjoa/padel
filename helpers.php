function renderView($viewName, $data = []) {
    extract($data);  // Convierte el array $data en variables individuales
    include __DIR__ . "/views/{$viewName}.php";  // Incluir el archivo de vista
}
