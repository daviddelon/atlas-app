<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommuneTest extends TestCase
{
    /**
     * Test retrieving bordering communes for a given commune using existing data.
     */
    public function test_get_bordering_communes_for_34274()
    {
        $targetCode = '34274';

        // Query for bordering communes
        $bordering = DB::select("
            SELECT code, nom
            FROM communes
            WHERE ST_Touches(geom, (SELECT geom FROM communes WHERE code = ?))
              AND code != ?
        ", [$targetCode, $targetCode]);

        // Expected bordering communes based on existing data
        $expectedCodes = ['34152', '34185', '34342', '34343', '34042', '34012', '34060'];

        $this->assertCount(7, $bordering);
        $actualCodes = array_column($bordering, 'code');
        sort($actualCodes);
        sort($expectedCodes);
        $this->assertEquals($expectedCodes, $actualCodes);
    }
}