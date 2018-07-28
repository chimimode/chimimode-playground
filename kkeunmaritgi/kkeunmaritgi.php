<?
    class Word{
        public $key = 'INPUT YOUR KEY';
        public $apiUrl = 'https://opendict.korean.go.kr/api/search';
        public $target_type = 'search';
        public $part = 'word';
        public $sort = array('dict', 'popular', 'date');
        public $start = 1;
        public $num = 10;

        /*
        끝단어로 시작하는 2-5음절 단어를 가져온뒤 랜덤으로 하나를 반환
        api 명세서 : https://opendict.korean.go.kr/service/openApiInfo
        */

        public function getWord($word){
            $wordLen = mb_strlen($word,"utf-8");
            $q = iconv_substr($word, $wordLen-1, $wordLen, "utf-8");

            shuffle($this->sort);

            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'content' => http_build_query(
                        array(
                            'key'           => $this->key,
                            'target_type'   => $this->target_type,
                            'part'          => $this->part,
                            'q'             => $q,
                            'sort'          => $this->sort[0],
                            'start'         => $this->start,
                            'num'           => $this->num,
                            'advanced'      => 'y', //자세히 검색이 있어야 이거 하위가 적용됨..ㅡㅡ
                            'pos'           => '1',
                            'method'        => 'start',
                            'type1'         => 'word',
                            'type2'         => 'all',
                            'type3'         => 'general',
                            'letter_s'      => 2,
                            'letter_e'      => 5,
                            //'cat'           => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41,42,43,44,45,47,48,49,50,51,52,53,54,55,56,57,59,60,61,62,63,64,65,66,67'
                            'cat'           => '0'
                        )
                    ),
                    'timeout' => 60
                )
            ));

            $data = file_get_contents($this->apiUrl, false, $context);
            $xml = simplexml_load_string($data);

            $result = json_decode(json_encode($xml),true);
            $elementCount  = $result['num'];

            if($elementCount > 1){
                $result = $result['item'];
                shuffle($result);
                $result = json_encode(str_replace('-', '', $result[0]));
            }else{
                $result = $result['item'];
                $result = json_encode(str_replace('-', '', $result));
            }

            return $result;
        }

        public function checkWord($word){
            //유저가 입력한 단어 체크용
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'content' => http_build_query(
                        array(
                            'key'           => $this->key,
                            'target_type'   => $this->target_type,
                            'part'          => $this->part,
                            'q'             => $word,
                            'sort'          => $this->sort[0],
                            'start'         => $this->start,
                            'num'           => 10,
                            'advanced'      => 'y', //자세히 검색이 있어야 이거 하위가 적용됨..ㅡㅡ
                            'pos'           => '1',
                            'method'        => 'start',
                            'type1'         => 'word',
                            'type2'         => 'all',
                            'type3'         => 'general',
                            'letter_s'      => 2,
                            'letter_e'      => 5,
                            //'cat'           => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41,42,43,44,45,47,48,49,50,51,52,53,54,55,56,57,59,60,61,62,63,64,65,66,67'
                            //구정물<- 이런 단어가 검색이 안되는 문제가있음 ㅡㅡ 왜그러지.. 나중ㅇ ㅔ확인해봐서 되는거같으면 다시 바꾸자
                            'cat'           => '0'
                        )
                    ),
                    'timeout' => 60
                )
            ));

            $data = file_get_contents($this->apiUrl, false, $context);
            $xml = simplexml_load_string($data);

            $result = json_decode(json_encode($xml),true);
            $elementCount  = $result['num'];
            $result = $elementCount;

            return $result;
        }
    }

    header("Content-Type: text/html; charset=UTF-8");
    $word = new Word();
    $type = $_POST['type'];

    if($type === 'search'){
        echo $word->getWord($_POST['word']);
    }else if($type === 'check'){
        echo $word->checkWord($_POST['word']);
    }else{
        echo '';
    }
?>
