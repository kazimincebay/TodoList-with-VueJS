<?php 
interface ITodoDal{
    function alltodos($user_id);
    function add($data);
    function update($data);
    function done($todo_id,$todo_user_id,$todo_status_id);
    function delete($todo_id,$todo_user_id);
}