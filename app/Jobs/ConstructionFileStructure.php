<?php

namespace App\Jobs;

use App\Helper\ConstuctionChiledStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConstructionFileStructure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id = null;
    private $typeDeductionArr = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $typeDeductionArr)
    {
        $this->id = $id;
        $this->typeDeductionArr = $typeDeductionArr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // constrions chiled
        $ConstuctionChiledStore = new ConstuctionChiledStore;
        $ConstuctionChiledStore->add_data_into_chiled($this->id, $this->typeDeductionArr);
    }
}
