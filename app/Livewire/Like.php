<?php


namespace App\Livewire;

use App\Models\Taxon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Maize\Markable\Models\Like as ModelsLike;
use App\Models\User;

class Like extends Component
{
    public Taxon $taxon;
    public bool $liked = false  ;



    public function mount(Taxon $taxon)
    {

        if (isset($taxon->like)) {
            $this->liked=true;
        }
    }


    public function toggle()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return;
        }


        ModelsLike::toggle($this->taxon, $user);
        $this->liked = ($this->liked  == true) ? false : true;

    }


    public function render() {
        return view('livewire.like');
    }
}


