<?php

namespace App\Console\Commands;

use App\Models\Lucky;
use DB;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class CrawlConstellation
 * @package App\Console\Commands
 */
class CrawlConstellation extends Command
{
    private $constellationArray = [
        '牡羊座',
        '金牛座',
        '雙子座',
        '巨蟹座',
        '獅子座',
        '處女座',
        '天秤座',
        '天蠍座',
        '射手座',
        '摩羯座',
        '水瓶座',
        '雙魚座',
    ];


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'constellation:crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '十二星座運勢爬蟲';


    /**
     * CrawlConstellation constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(' 開始於：' . date('Y/m/d H:i:s'));

        $today = date('Y-m-d');
        $crawler = new Crawler();

        foreach($this->constellationArray as $index => $constellation) {
            try {
                DB::beginTransaction();
                $luckyModel = Lucky::firstOrNew(['constellation_id' => $index + 1], ['at_day' => $today]);
                $html = file_get_contents("http://astro.click108.com.tw/daily.php?iAcDay={$today}&iAstro={$index}");
                $crawler->addHtmlContent($html);
                $crawler->filterXPath('//div[contains(@class, "TODAY_CONTENT")]')->filter('p')->each(function (Crawler $node, $i) use($luckyModel) {
                    switch ($i) {
                        // 整體運勢
                        case 0:
                            $luckyModel->average_fortune = substr_count($node->text(), '★');
                            break;
                        case 1:
                            $luckyModel->average_description = trim($node->text());
                            break;
                        // 愛情運勢
                        case 2:
                            $luckyModel->love_fortune = substr_count($node->text(), '★');
                            break;
                        case 3:
                            $luckyModel->love_description = trim($node->text());
                            break;
                        // 事業運勢
                        case 4:
                            $luckyModel->career_fortune = substr_count($node->text(), '★');
                            break;
                        case 5:
                            $luckyModel->career_description = trim($node->text());
                            break;
                        // 財運運勢
                        case 6:
                            $luckyModel->wealth_fortune = substr_count($node->text(), '★');
                            break;
                        case 7:
                            $luckyModel->wealth_description = trim($node->text());
                            break;
                    }

                });
                $luckyModel->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error(' 錯誤: ' . $e->getMessage());
            } finally {
                $crawler->clear();
            }

        }
        $this->info(' 結束於：' . date('Y/m/d H:i:s'));
    }
}