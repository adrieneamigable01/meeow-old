<?php
    class schedulemodel extends CI_Model{
        public function all($borrower_id){
            $sql = "SELECT schedules.id,
                            schedules.name as title,
                            schedules.borrower_id,
                            -- DATE_FORMAT(schedules.start,'%H:%i') as title,
                            schedules.start,
                            schedules.end,
                            CASE
                                WHEN schedules.borrower_id = {$borrower_id} THEN ''
                                ELSE 'grey'
                            END as color
                    FROM schedules
                    WHERE schedules.is_active = 1";
            return $this->db->query($sql)->result();
        }
        public function info($id){
            $sql = "SELECT schedules.id,
                            schedules.name,
                            schedules.title,
                            schedules.start,
                            schedules.end,
                            borrower_contact.email,
                            borrower_contact.mobile,
                            district.name as 'district_name'
                    FROM schedules
                    LEFT JOIN borrower          ON borrower.borrower_id          = schedules.borrower_id 
                    LEFT JOIN borrower_contact  ON borrower_contact.borrower_id  = schedules.borrower_id 
                    LEFT JOIN district          ON district.district_id          = borrower.district_id
                    WHERE schedules.id = {$id}";
            $res =  $this->db->query($sql)->result();
            if($this->db->query($sql)->num_rows() > 0){
                return $res[0];
            }else{
                return $res;
            }
        }
        public function check_schedule($borrower_id,$date){
            $sql = "SELECT schedules.id
                    FROM schedules
                    WHERE schedules.borrower_id = {$borrower_id} AND DATE_FORMAT(schedules.start,'%Y/%m/%d') = '{$date}' AND schedules.is_active = 1";
            return $this->db->query($sql)->num_rows();
        }
        public function add($payload){
            return $this->db->insert("schedules",$payload);
        }
        public function update($payload,$id){
            $this->db->where("id",$id);
            return $this->db->update("schedules",$payload);
        }
        public function get_available_date($date){
            $sql = "SELECT DATE_FORMAT(schedules.start,'%H:%i') as start,
                            schedules.end
                    FROM schedules 
                    WHERE DATE_FORMAT(schedules.start,'%Y/%m/%d') = '{$date}' AND schedules.is_active = 1";
            return $this->db->query($sql)->result();
        }
        public function getAll($date){
            $sql = "SELECT schedules.id,
                            schedules.name,
                            schedules.title,
                            schedules.start,
                            schedules.end,
                            schedules.status_id,
                            schedules.borrower_id,
                            borrower_contact.email,
                            borrower_contact.mobile,
                            district.name as 'district_name'
                    FROM schedules
                    LEFT JOIN borrower          ON borrower.borrower_id          = schedules.borrower_id 
                    LEFT JOIN borrower_contact  ON borrower_contact.borrower_id  = schedules.borrower_id 
                    LEFT JOIN district          ON district.district_id          = borrower.district_id
                    WHERE DATE_FORMAT(schedules.start,'%Y/%m/%d') = '{$date}' AND schedules.is_active = 1
                    ORDER BY schedules.start ASC";
            return $this->db->query($sql)->result();
        }
        public function count_entries($date){
            $sql = "SELECT
                        COUNT(schedules.id) as total,
                        schedules.borrower_id
                    FROM
                        schedules
                    WHERE
                        schedules.is_active = 1 AND DATE_FORMAT(schedules.start,'%Y/%m') = '{$date}' AND schedules.status_id = 1
                    GROUP BY
                        schedules.borrower_id";
            return $this->db->query($sql)->result();
            
        }
        public function test(){
            // $sq = "
            // SELECT
            //     CONCAT(
            //         borrower.lastname,
            //         ',',
            //         borrower.firstname,
            //         ' ',
            //         borrower.middlename
            //     ) AS NAME,
            //     district.name,
            //     (
            //     SELECT
            //         COUNT(*)
            //     FROM
            //         schedules
            //     WHERE
            //         schedules.borrower_id = borrower.borrower_id
            //     ) AS COUNT
            // FROM
            //     borrower
            // LEFT JOIN district ON district.district_id = borrower.district_id"
        }
    }
?>
