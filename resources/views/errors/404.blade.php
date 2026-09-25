@include('errors.layout', [
    'code'    => 404,
    'title'   => 'Page introuvable',
    'message' => "La page que vous cherchez n'existe pas ou a été déplacée.",
])
