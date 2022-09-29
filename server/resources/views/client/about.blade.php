@extends('layouts.master')

@section('content')

<div class="col-lg-7 mt-5 mt-lg-0 d-flex align-items-stretch">
  <form action="forms/contact.php" method="post" role="form" class="php-email-form">
    <div class="row">
      <div class="form-group col-md-6">
        <label for="name">Your Name</label>
        <input type="text" name="name" class="form-control" id="name" required>
      </div>
      <div class="form-group col-md-6">
        <label for="name">Your Email</label>
        <input type="email" class="form-control" name="email" id="email" required>
      </div>
    </div>
    <div class="form-group">
      <label for="name">Subject</label>
      <input type="text" class="form-control" name="subject" id="subject" required>
    </div>
    <div class="form-group">
      <label for="name">Message</label>
      <textarea class="form-control" name="message" rows="10" required></textarea>
    </div>
    <div class="my-3">
      <div class="loading">Loading</div>
      <div class="error-message"></div>
      <div class="sent-message">Your message has been sent. Thank you!</div>
    </div>
    <div class="text-center"><button type="submit">Send Message</button></div>
  </form>
</div>
@endsection