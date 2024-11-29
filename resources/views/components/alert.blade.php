@props(['type'=>'danger', 'message'=>''])

<div class="alert alert-{{ $type }}  alert-dismissible fade show mt-1" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    {{ session($message) }}
</div>