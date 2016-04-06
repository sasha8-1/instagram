<?php if (!defined('BASEPATH')) exit('Нет доступа к скрипту');
class Vk {

    function __construct($data)
    {
        $this->ci = &get_instance();
        $this->ci->load->helper('request');
        $this->ci->load->library('phpquery/phpquery');
        $this->data = $data;
    }

    public function getData() {
        $data = array();
        foreach($this->data as $key => $value) {
            $data = $this->getOwnerId($value->url);
            $html = $this->getHTML($data);
            $arrayItem = $this->getItem($html);

        }

    }
    private function getHTML($data) {
        $html = SendRequest(array(
            "url" => "http://vk.com/al_wall.php",
            "post" => [
                "act" => "get_wall",
                "al" => 1,
                "offset" => 0,
                "owner_id" => $data['owner_id'],
                "fixed" => $data['fixed'],
                "type" => "own"
            ]
        ));
        return iconv('windows-1251', 'UTF-8', $html[1]);
    }
    private function getOwnerId($url) {
        $response = SendRequest(array("url" => $url));
        preg_match("/data-post-id=\"(.+?)_(.+?)\"/", $response[1], $matches);
        return array(
            "owner_id" => $matches[1],
            "fixed" => $matches[2]
            );
    }
    private function getItem($html) {

        preg_match("/.+?(<div.*<\/div>)/s", $html, $matches);
//        print_r($matches);
        $document = $this->ci->phpquery->newDocumentHTML("<html><head></head><body>".$matches[1]."</body></html>");
        $items = $document->find('div.post_table');

        foreach ($items as $el) {
            $images = pq($el)->find("div.page_post_sized_thumbs")->html();
            if ($images) {
                $dates = pq($el)->find("span.rel_date")->attr("time");
                $title = pq($el)->find("div.wall_post_text")->html();
                print_r($dates);
                print_r($title);
            }


            echo "<hr />";


            print_r($images);
        }


    }
}