<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Support\Str;

class BookingService
{
    public function createBooking(array $data): Booking
    {
        $bookingCode = $this->generateBookingCode();
        $totalPrice = $this->calculateTotalPrice($data);

        return Booking::create([
            'flight_schedule_id' => $data['flight_schedule_id'],
            'booking_code' => $bookingCode,
            'full_name' => $data['full_name'],
            'nik' => $data['nik'],
            'seat_class' => $data['seat_class'],
            'passenger_count' => $data['passenger_count'],
            'ancillary_services' => $data['ancillary_services'] ?? [],
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);
    }

    public function confirmPayment(int $bookingId, string $paymentStatus, string $paymentMethod): bool
    {
        $booking = Booking::find($bookingId);

        if (!$booking || $booking->status !== 'pending' || $paymentStatus !== 'successful') {
            return false;
        }

        $booking->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Generate ticket
        $this->generateTicket($booking);

        return true;
    }

    private function generateTicket(Booking $booking): void
    {
        // Prevent duplicate tickets
        if ($booking->tickets()->exists()) {
            return;
        }

        Ticket::create([
            'booking_id' => $booking->id,
            'ticket_number' => $this->generateTicketNumber(),
            'passenger_name' => $booking->full_name,
            'seat_number' => $this->assignSeatNumber($booking),
        ]);
    }

    private function generateBookingCode(): string
    {
        do {
            $code = 'BK' . strtoupper(Str::random(8));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    private function generateTicketNumber(): string
    {
        do {
            $number = 'TK' . strtoupper(Str::random(10));
        } while (Ticket::where('ticket_number', $number)->exists());

        return $number;
    }

    private function assignSeatNumber(Booking $booking): string
    {
        // Simple seat assignment logic - in real app, this would be more complex
        $seatClass = $booking->seat_class;
        $prefix = match ($seatClass) {
            'economy' => 'E',
            'business' => 'B',
            'first_class' => 'F',
            default => 'E',
        };

        return $prefix . rand(1, 50);
    }

    private function calculateTotalPrice(array $data): float
    {
        // This is a simplified calculation - in real app, get from flight data
        $basePrice = 1000000; // Example price
        $multiplier = match ($data['seat_class']) {
            'economy' => 1,
            'business' => 2,
            'first_class' => 3,
            default => 1,
        };

        $total = $basePrice * $multiplier * $data['passenger_count'];

        // Add ancillary services
        if (isset($data['ancillary_services'])) {
            foreach ($data['ancillary_services'] as $service) {
                $total += match ($service) {
                    'travel_insurance' => 50000,
                    'extra_baggage' => 100000,
                    default => 0,
                };
            }
        }

        return $total;
    }
}