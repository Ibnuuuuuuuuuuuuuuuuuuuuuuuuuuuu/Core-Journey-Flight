%% Sequence Diagram untuk US 2.5: Generate E-Ticket
sequenceDiagram
    participant User
    participant Controller as BookingController
    participant Service as BookingService
    participant DB as Database (bookings, tickets)
    participant PDF as PDF Generator

    User->>Controller: Konfirmasi Pembayaran (booking_id)
    Controller->>Service: processPayment(booking_id)
    Service->>DB: Update status 'Paid'
    Service->>DB: Insert new Ticket (ticket_code, qr_url)
    Service->>PDF: Generate E-Ticket PDF
    PDF-->>Service: Return PDF Path
    Service-->>Controller: Return Success Data
    Controller-->>User: Tampilkan View Success & E-Ticket (Downloadable)