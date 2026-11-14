<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BookingHistory extends Model {
 protected $fillable=['booking_id','admin_id','from_status','to_status','note'];
 public function booking(){return $this->belongsTo(Booking::class);}
}
