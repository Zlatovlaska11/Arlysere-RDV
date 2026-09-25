@include('errors.layout', [
    'code'    => 403,
    'title'   => 'Accès refusé',
    'message' => $exception->getMessage() ?: "Vous n'avez pas les droits pour accéder à cette page.",
])
