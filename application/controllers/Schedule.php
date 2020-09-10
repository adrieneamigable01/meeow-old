<?php
    date_default_timezone_set('Asia/Manila');
    class Schedule extends CI_Controller{
        public function __construct() {
            parent::__construct();
            $this->load->model('ScheduleModel','schedulemodel');
        }
        public function all($borrower_id){
            $response = array();
            echo json_encode($this->schedulemodel->all($borrower_id));
        }
        public function add(){
            $response       = array();
            $datetime       = date("Y/m/d",strtotime($this->input->post("_date")));
            $date           = date("Y/m/d",strtotime($this->input->post("_date")));
            $time           = date("H:i:s",strtotime($this->input->post("time")));
            $borrower_id    = $this->input->post("borrower_id");
            $end            = strtotime("+5 minutes", strtotime($time));
            $cheack         = $this->schedulemodel->check_schedule($borrower_id,$date);
            // print_r($cheack);exit;
            if($cheack > 0){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'This user already have a schedule on this date',
                );
            }else{
                if(empty($date)){
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => 'Empty date',
                    );
                }
                else if(empty($borrower_id)){
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => 'Empty borrower id',
                    );
                }
                else{
                    try {
                        $payload = array(
                            'borrower_id'       => $borrower_id,
                            'name'              => $this->input->post("name"),
                            'title'             => $this->input->post("title"),
                            'start'             => $date.' '.$time,  
                            'end'               => $date.' '.date("H:i:s",$end),  
                        );
                        $data = $this->schedulemodel->add($payload);
                        if($data){
                            $response = array(
                                'isError'   => false,
                                'data'      => $payload,
                                'date_request' =>$this->input->post("_date"),
                                'date'      => date("Y-m-d"),
                                'message'   => 'Successuly plotted new schedule'
                            );
                        }else{
                            $response = array(
                                'isError'   => true,
                                'data'      => $payload,
                                'date'      => date("Y-m-d"),
                                'message'   => 'Error plotting schedule'
                            );
                        }
                    }
                    catch(Exception $e) {
                        $response = array(
                            'isError'   => true,
                            'data'      => '',
                            'date'      => date("Y-m-d"),
                            'message'   => $e->getMessage(),
                        );
                    }
                }
            }
            
            $this->displayJSON($response); 
        }   
        public function update(){
            $response       = array();
            $date           = date("Y/m/d",strtotime($this->input->post("_date")));
            $time           = date("H:i:s",strtotime($this->input->post("time")));
            $borrower_id    = $this->input->post("borrower_id");
            $id             = $this->input->post("id");
            $title          = $this->input->post("title");
            $isAdmin        = empty($this->input->post("isAdmin")) ? false : $this->input->post("isAdmin");
            $end            = strtotime("+5 minutes", strtotime($time));
            $cheack         = $this->schedulemodel->check_schedule($borrower_id,$date);
            // print_r($cheack);exit;
            if($cheack > 0 && !$isAdmin){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'This user already have a schedule on this date',
                );
            }else{  
                if(empty($date)){
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => 'Empty date',
                    );
                }
                else if(empty($id)){
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => 'Empty schedule id',
                    );
                }
                else{
                    try {
                        $payload = array(
                            'start'             => $date.' '.$time,  
                            'end'               => $date.' '.date("H:i:s",$end),  
                        );
                        if(!empty($title)){
                            $payload['title'] = $title;
                        }
                        $data = $this->schedulemodel->update($payload,$id);
                        $response = array(
                            'isError'   => false,
                            'date_request'      => $date,
                            'data'      => $data,
                            'date'      => date("Y-m-d"),
                            'message'   => 'Successuly update the plotted schedule'
                        );
                    }
                    catch(Exception $e) {
                        $response = array(
                            'isError'   => true,
                            'data'      => '',
                            'date'      => date("Y-m-d"),
                            'message'   => $e->getMessage(),
                        );
                    }
                }
            }
            $this->displayJSON($response); 
        }
        public function void(){
            $response       = array();
            $id             = $this->input->post("id");
            // print_r($id);exit;
            // print_r($cheack);exit;
            if(empty($id)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty schedule id',
                );
            }
            else{
                try {
                    $payload = array(
                        'is_active'         => 0,  
                        'description'       => $this->input->post("description"),  
                        'void_date'         => date("Y-m-d H:i:s"),  
                    );
                    $data = $this->schedulemodel->update($payload,$id);
                    $response = array(
                        'isError'   => false,
                        'data'      => $data,
                        'date'      => date("Y-m-d"),
                        'message'   => 'Successuly void schedule'
                    );
                }
                catch(Exception $e) {
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }
            $this->displayJSON($response); 
        }
        public function status(){
            $response       = array();
            $id             = $this->input->post("id");
            try {
                $payload = array(
                    'status_id'             => $this->input->post("status_id"),  
                );
                $data = $this->schedulemodel->update($payload,$id);
                $response = array(
                    'isError'       => false,
                    'data'          => $data,
                    'date'          => date("Y-m-d"),
                    'message'       => 'Successuly update schedule status'
                );
            }
            catch(Exception $e) {
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => $e->getMessage(),
                );
            }
            $this->displayJSON($response); 
        }
        public function count_entries(){
            $response       = array();
            $date           = date("Y/m",strtotime($this->input->post("date")));
            if(empty($date)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty date',
                );
            }else{
                try {
                    $data = $this->schedulemodel->count_entries($date);
                    $response = array(
                        'isError'       => false,
                        'request_date'  => $date,
                        'data'          => $data,
                        'date'          => date("Y-m-d"),
                        'message'       => 'Success'
                    );
                }
                catch(Exception $e) {
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }
            $this->displayJSON($response); 
        }
        public function info(){
            $response   = array();
            $id       = $this->input->post("id");
            if(empty($id)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty Schedule id',
                );
            }else{
                try {
                    $data = $this->schedulemodel->info($id);
                    $response = array(
                        'isError'   => false,
                        'data'      => $data,
                        'date'      => date("Y-m-d"),
                        'message'   => 'Success'
                    );
                }
                catch(Exception $e) {
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }
            $this->displayJSON($response); 
        }
        public function check_schedule(){
            $response   = array();
            $borrower_id       = $this->input->post("borrower_id");
            $date               = $this->input->post("date");
            if(empty($date)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty Date',
                );
            }
            else if(empty($borrower_id)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty borrower id',
                );
            }
            else{
                try {
                    $data = $this->schedulemodel->check_schedule($borrower_id,$date);
                    if($data > 0 ){
                        $response = array(
                            'isError'   => true,
                            'date'      => date("Y-m-d"),
                            'message'   => 'You already have schedule on this date'
                        );
                    }else{
                        $response = array(
                            'isError'   => false,
                            'date'      => date("Y-m-d"),
                            'message'   => 'Success'
                        );
                    }
                }
                catch(Exception $e) {
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }
            $this->displayJSON($response); 
        }
        public function get_available_date(){
            $response   = array();
            $date       = date("Y/m/d",strtotime($this->input->post("date")));
            if(empty($date)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty Date',
                );
            }else{
                try {
                    $data = $this->schedulemodel->get_available_date($date);
                    $response = array(
                        'isError'   => false,
                        'data'      => $data,
                        'date'      => date("Y-m-d"),
                        'message'   => 'Success'
                    );
                }
                catch(Exception $e) {
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }
            $this->displayJSON($response); 
        }
        public function getAll(){
            $response   = array();
            $date       = $this->input->post("date");
            if(empty($date)){
                $response = array(
                    'isError'   => true,
                    'data'      => '',
                    'date'      => date("Y-m-d"),
                    'message'   => 'Empty Date',
                );
            }else{
                $date       = date("Y/m/d",strtotime($date));
                try {
                    $data = $this->schedulemodel->getAll($date);
                    $response = array(
                        'isError'   => false,
                        'data'      => $data,
                        'date_request'=>$date,
                        'date'      => date("Y-m-d"),
                        'message'   => 'Success'
                    );
                }
                catch(Exception $e) {
                    $response = array(
                        'isError'   => true,
                        'data'      => '',
                        'date_request'=>$date,
                        'date'      => date("Y-m-d"),
                        'message'   => $e->getMessage(),
                    );
                }
            }

            $this->displayJSON($response);
        }
        private function displayJSON($data){
            if(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")){
                header('Content-Type: application/json');
            }
            else{
                header('Content-Type: application/json');
                header('Access-Control-Allow-Methods: GET, POST');
                header('Access-Control-Allow-Origin: *');
                header("Cache-Control: no-cache");
                header("Pragma: no-cache");
                echo json_encode($data);
            }
           
        }
    }
?>