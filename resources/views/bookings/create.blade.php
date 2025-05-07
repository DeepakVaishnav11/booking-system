@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
        @if ($errors->any())
            <div class="alert alert-danger d-flex justify-content-between">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

            <div class="card">
                <div class="card-header">{{ __('Bookings') }}</div>

                <div class="card-body">

                <form method="POST" action="{{route('bookings.store')}}" id="booking-frm">
                        @csrf
                        <div class="form-group">
                            <label for="customer_name">Name</label>
                            <input type="text" class="form-control mb-2" name="customer_name"   required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email ">Email</label>
                            <input type="text" class="form-control mb-2" name="customer_email"   required>
                        </div>
                        <div class="form-group">
                            <label for="booking_date ">Booking Date</label>
                            <input type="text" class="form-control mb-2" name="booking_date" id="booking_date" required>
                        </div>

                        <div class="form-group">
                            <label for="booking_type mb-2">Booking Type</label>
                            <select class="form-control mb-2" id="booking_type" name="booking_type" required onchange="handleBookingTypeChange()">
                                <option value="">Select Type</option>
                                <option value="Full Day">Full Day</option>
                                <option value="Half Day">Half Day</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>  

                        <div class="form-group mb-2" id="slot_group" style="display:none;">
                            <label for="booking_slot">Booking Slot</label>
                            <select class="form-control" id="booking_slot" name="booking_slot">
                                <option value="">Select Slot</option>
                                <option value="First Half">First Half</option>
                                <option value="Second Half">Second Half</option>
                            </select>
                        </div>

                        <!-- <div class="form-group  mt-2" id="time_group" style="display:none;">
                            <label for="from_time">From Time</label>
                            <input type="text" class="form-control" id="from_time" name="from_time">

                            <label for="to_time">To Time</label>
                            <input type="text" class="form-control" id="to_time" name="to_time">
                        </div> -->

                        <div class="form-group mt-2" id="time_group" style="display: none;">
                            <div class="row">
                                <!-- From Time -->
                                <div class="col-md-6">
                                    <label for="from_time">From Time</label>
                                    <input type="text" class="form-control" id="from_time" name="from_time">
                                </div>

                                <!-- To Time -->
                                <div class="col-md-6">
                                    <label for="to_time">To Time</label>
                                    <input type="text" class="form-control" id="to_time" name="to_time">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Submit Booking</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleBookingTypeChange() {
    const type = document.getElementById('booking_type').value;
    const slotGroup = document.getElementById('slot_group');
    const timeGroup = document.getElementById('time_group');

    slotGroup.style.display = (type === 'Half Day') ? 'block' : 'none';
    timeGroup.style.display = (type === 'Custom') ? 'block' : 'none';
}
</script>
 

<script>
  $(document).ready(function(){

    $('#booking-frm').validate({
        rule:{},
        ignore:[],
    });

    $('#from_time, #to_time').timepicker({
        timeFormat: 'HH:mm:ss', // Matches MySQL TIME format
        interval: 30,
        minTime: '08:00',
        maxTime: '22:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });

    $('#booking_date').datepicker({minDate:0});
    
  });

</script>
@endsection
