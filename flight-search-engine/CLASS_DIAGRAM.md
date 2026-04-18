# Class Diagram - Journey 2 (Booking to E-Ticket)

Dokumen ini merinci struktur kelas untuk implementasi US 2.2 hingga 2.5, yang merupakan kelanjutan dari fitur pencarian (US 2.1).

## Mermaid Diagram

```mermaid
classDiagram
    %% Hubungan dengan User Story 2.1
    FlightSearchController ..> BookingController : Redirect (Pilih Tiket)

    %% Controller & Request
    class BookingController {
        -BookingService $service
        +showPassengerForm(scheduleId) View (US 2.2)
        +storeBooking(BookingRequest $request) Response (US 2.3)
        +checkout(bookingId) View (US 2.4)
        +confirmPayment(bookingId) Response (US 2.5)
    }

    class BookingRequest {
        +rules() array (Validasi NIK/Nama)
    }

    %% Service & Repository
    class BookingService {
        -BookingRepository $repository
        +createBooking(data)
        +validatePaymentTimer(bookingId) (US 2.4)
        +issueTicket(bookingId) (US 2.5)
    }

    class BookingRepository {
        +save(Booking $booking)
        +findWithDetails(id)
    }

    %% Models
    class Booking {
        -string booking_code
        -enum status
        -datetime payment_expired_at
    }
    class Passenger {
        -string nik
        -string full_name
    }

    %% Relations
    BookingController --> BookingRequest : validates
    BookingController --> BookingService : delegates
    BookingService --> BookingRepository : calls
    BookingRepository --> Booking : manages
    Booking "1" -- "*" Passenger : contains
```
