<div>

<span wire:click="toggle" class="cursor-pointer" style="cursor: pointer;">
    @svg(
        $liked ? 'heroicon-o-exclamation-triangle' : 'heroicon-s-exclamation-triangle',
        [
            'width' => '20',
            'height' => '20',
            'fill' => $liked ? '#ff0000' : '#6c757d'
        ]
    )
</span>

</div>
