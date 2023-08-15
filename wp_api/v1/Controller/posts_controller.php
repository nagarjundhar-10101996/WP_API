<?php
class posts_controller{
    public function __construct(private posts_model $model)
    {
    }
    public function procreq(string $meth , ?array $id):void{
        if(is_array($id))
        {
           switch (count($id)) {
            case '1':
                $this->procreqdata($meth);
                break;
            case '2':
                $this->procreqpager($meth,$id[count($id)-1]);
                break;
            default:
                # code...
                break;
           } 
        }
        else
        {
            die('URI to Array required ');
        }
    }
    public function procreqdata(string $meth):void{
        switch ($meth) {
            case 'GET':
                echo json_encode(['status'=>200,'reason'=>'Sucess','data'=>['total_pages'=>$this->model->totalpage(),'records'=>$this->model->getAll()]]);
                break;
            default:
                header("HTTP/1.1 404 Not Found");
                break;
        }
    }
    public function procreqpager(string $meth , ?string $id):void{
        
        switch ($meth) {
            case 'GET':
                if(!empty($id))
                {
                    echo json_encode(['status'=>200,'reason'=>'Sucess','data'=>['page'=>intval(explode('?',strval($id))[0]),'total_pages'=>$this->model->totalpage(),'number_of_records'=>(empty(strpos($id, '?')))?posts_model::$pageper:1,'records'=>$this->model->getpage($id)]]);
                }
                else{
                    header("HTTP/1.1 404 Not Found");
                }
                break;
            case 'POST':
                if($id == 'update')
                {
                    $data =  json_decode(file_get_contents("php://input"),true) ;
                    $payload = Token::Verify($this->model->update($data), $_SERVER['HTTP_AUTHORIZATION']);
                    echo json_encode(['status'=>200,'reason'=>'Success','data'=>['page'=>'1','total_pages'=>$this->model->totalpage(),'number_of_records'=>1,'records'=>$payload]]);
                }
                else
                {
                    header("HTTP/1.1 404 Not Found");
                }
                break;
            default:
            header("HTTP/1.1 404 Not Found");
                break;
        }
        
    }
}
?>
