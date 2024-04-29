<?php

/**
 * View to display the form
 * @since   1.0.0
 * @author  Ridwan Arifandi
 */

if (!defined('ABSPATH')) exit;
?>

<form id="incsub-form" class="incsub-form" action="" method="post">
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="form-control" required>
  </div>
  <div class="form-group
  ">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="phone">Phone</label>
    <input type="text" name="phone" id="phone" class="form-control" required>
  </div>
  <div class="form-group">
    <label for="address">Address</label>
    <textarea name="address" id="address" class="form-control" required></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>

  <div class="alert alert-danger mt-3" style="display: none;"></div>
</form>