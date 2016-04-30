<?php if (!defined('BASEPATH')) exit('Нет доступа к скрипту');

class Vk
{

    private $ci;
    private $data;

    private $versionApi = 5.7;
    private $urlApi = 'http://api.vk.com/method/';

    function __construct($data)
    {
        $this->ci = &get_instance();
        $this->ci->load->helper('request');
        $this->data = $data;
    }

    public function getImages() {
        $result = [];
        foreach ($this->data as $key => $value) {
            $timestamp = (new DateTime($value->last_update))->getTimestamp();
            $data = $this->getGroup($timestamp, $value->url, 0);
            $likes = $this->getAverageLikes($data);
            $images = $this->filterImages($data, $timestamp, $likes);
            $result = array_merge($result, $images);
        }
        return $result;
    }

    private function getAverageLikes($data) {
        $likes = 0;
        foreach($data as $key => $value) {
            $likes += $value->likes->count;
        }
        return $likes / count($data);
    }

    private function filterImages($data, $timestamp, $likes) {

        $options = new stdClass;
        $options->timestamp = $timestamp;
        $options->likes = $likes;
        $result = array_filter($data, function ($item) use ($options) {
            return $item->date > $options->timestamp && isset($item->attachments) && isset($item->likes) && $item->likes->count > $options->likes;
        });

        $images = [];
        foreach($result as $key => $value) {
            $item = new stdClass;
            $item->text = $value->text;
            $item->url = $value->attachments[0]->photo->photo_604;
            array_push($images, $item);
        }

        return $images;
    }

    private function getGroup($timestamp, $url, $offset = 0)
    {
        $count = 100;
        $data = SendRequest(array(
            "url" => $this->urlApi."wall.get",
            "get" => [
                "v" => $this->versionApi,
                'filter' => 'all',
                'domain' => $url,
                'count' => $count,
                'offset' => $offset
            ]
        ));
        $posts = json_decode($data[1])->response->items;
        $lastItem = array_slice($posts,-1,1);
        $lastDate = $lastItem[0]->date;

        if ($lastDate > $timestamp) {
            $offset+=$count;
            $posts = array_merge($posts, $this->getGroup($timestamp, $url, $offset));
        }

        return $posts;
    }

}