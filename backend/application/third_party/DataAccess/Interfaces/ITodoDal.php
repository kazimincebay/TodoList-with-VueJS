<?php 
interface ITodoDal{
    function alltodos();
    function add($data);
    function update($data);
    function delete($id);
}