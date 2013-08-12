<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file assignment_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Assignment_model extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}


	/*
	 * Adds new assignment to database
	 */
	public function add_assignment($id,$edit=FALSE){
		// Adding assignment to "assignments" table (or editing existing assignment)
		$assignment = array(
			'id' => $id,
			'name' => $this->input->post('assignment_name'),
			'problems' => $this->input->post('number_of_problems'),
			'total_submits' => 0,
			'open' => ($this->input->post('open')===FALSE?0:1),
			'scoreboard' => ($this->input->post('scoreboard')===FALSE?0:1),
			'description' => "", /* todo */
			'start_time' => date('Y-m-d H:i:s',strtotime($this->input->post('start_time'))),
			'finish_time' => date('Y-m-d H:i:s',strtotime($this->input->post('finish_time'))),
			'extra_time' => $this->input->post('extra_time'),
			'late_rule' => $this->input->post('late_rule'),
			'participants' => $this->input->post('participants')
		);
		if($edit){
			unset($assignment['total_submits']);
			$this->db->where('id',$id)->update('assignments',$assignment);
		}
		else
			$this->db->insert('assignments',$assignment);

		// Adding problems to "problems" table

		//first remove all previous problems
		$this->db->delete('problems',array('assignment'=>$id));

		//now add new problems:
		$names = $this->input->post('name');
		$scores = $this->input->post('score');
		$c_tl = $this->input->post('c_time_limit');
		//$py_tl = $this->input->post('python_time_limit');
		$java_tl = $this->input->post('java_time_limit');
		$ml = $this->input->post('memory_limit');
		$ft = $this->input->post('filetypes');
		$dc = $this->input->post('diff_cmd');
		$da = $this->input->post('diff_arg');
		for ($i=1;$i<=$this->input->post('number_of_problems');$i++){

			$problem = array(
				'assignment' => $id,
				'id' => $i,
				'name' => $names[$i-1],
				'score' => $scores[$i-1],
				'judge' => in_array($i,$this->input->post('judge'))?1:0,
				'c_time_limit' => $c_tl[$i-1],
				//'python_time_limit' => $py_tl[$i-1],
				'java_time_limit' => $java_tl[$i-1],
				'memory_limit' => $ml[$i-1],
				'allowed_file_types' => $ft[$i-1],
				'diff_cmd' => $dc[$i-1],
				'diff_arg' => $da[$i-1],
			);
			$this->db->insert('problems',$problem);
		}
	}


	/*
	 * Returns a list of all assignments and their information
	 */
	public function all_assignments(){
		return $this->db->get('assignments')->result_array();
	}

	/*
	 * Returns id of last assignment (the largest assignment id). Used for adding new assignment.
	 */
	public function last_assignment_id(){
		$assignments = $this->db->select('id')->get('assignments')->result_array();
		$max=0;
		foreach ($assignments as $assignment){
			if ($assignment['id']>$max)
				$max = $assignment['id'];
		}
		return $max;
	}

	public function all_problems($assignment_id){
		return $this->db->get_where('problems',array('assignment'=>$assignment_id))->result_array();
	}

	public function problem_info($assignment_id, $problem_id){
		return $this->db->get_where('problems',array('assignment'=>$assignment_id,'id'=>$problem_id))->row_array();
	}


	/*
	 * Returns info about given assignment
	 */
	public function assignment_info($assignment_id){
		$query = $this->db->get_where('assignments',array('id'=>$assignment_id));
		if ($query->num_rows()!=1)
			return array(
				'id'=>0,
				'name'=>'Not Selected',
				'finish_time' => 0,
				'extra_time' => 0
			);
		return $query->row_array();
	}


	/*
	 * Returns TRUE if $username if one of the $participants
	 * Examples for participants: "ALL" or "user1, user2,user3"
	 */
	public function is_participant($participants, $username){
		$participants = explode(",",$participants);
		foreach ($participants as &$participant){
			$participant = trim($participant);
		}
		if(in_array("ALL",$participants))
			return TRUE;
		if(in_array($username,$participants))
			return TRUE;
		return FALSE;
	}

	public function add_total_submits($assignment_id){
		$total = $this->db->select('total_submits')->get_where('assignments',array('id'=>$assignment_id))->row()->total_submits;
		$this->db->where('id',$assignment_id)->update('assignments',array('total_submits'=>($total+1)));
		return ($total+1);
	}

	public function set_moss_time($assignment_id){
		$now = mdate("%Y-%m-%d %H:%i:%s",shj_now());
		$this->db->where('id',$assignment_id)->update('assignments',array('moss_update'=>$now));
	}

	public function get_moss_time($assignment_id){
		return $this->db->select('moss_update')->get_where('assignments',array('id'=>$assignment_id))->row()->moss_update;
	}

}