<?php
// data/DataLayer.php
class DataLayer
{
    private $conn;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->conn = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    // Clean input
    private function clean($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    // Clients
    public function getClients()
    {
        $result = $this->conn->query("SELECT * FROM clients");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getClientByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();
        $stmt->close();
        return $client;
    }

    public function addClient($first_name, $last_name, $email, $phone, $address)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO clients (first_name, last_name, email, phone, address) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $address);
        $success = $stmt->execute();
        $client_id = $success ? $this->conn->insert_id : null;
        $stmt->close();
        return $client_id;
    }

    public function updateClientEmail($client_id, $email)
    {
        $stmt = $this->conn->prepare("UPDATE clients SET email = ? WHERE client_id = ?");
        $stmt->bind_param("si", $email, $client_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteClient($client_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM clients WHERE client_id = ?");
        $stmt->bind_param("i", $client_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Services
    public function getServices()
    {
        $result = $this->conn->query("SELECT * FROM services");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getServiceByName($service_name)
    {
        $stmt = $this->conn->prepare("SELECT * FROM services WHERE service_name = ?");
        $stmt->bind_param("s", $service_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();
        $stmt->close();
        return $service;
    }

    public function addService($service_name, $description, $price, $duration)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO services (service_name, description, price, duration) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdi", $service_name, $description, $price, $duration);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateService($service_id, $price, $duration)
    {
        $stmt = $this->conn->prepare("UPDATE services SET price = ?, duration = ? WHERE service_id = ?");
        $stmt->bind_param("dii", $price, $duration, $service_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function deleteService($service_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM services WHERE service_id = ?");
        $stmt->bind_param("i", $service_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Appointments
    public function getAppointments()
    {
        $result = $this->conn->query(
            "SELECT a.appointment_id, c.first_name, c.last_name, s.service_name, a.appointment_date, a.status
             FROM appointments a
             JOIN clients c ON a.client_id = c.client_id
             JOIN services s ON a.service_id = s.service_id"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function checkAppointmentConflict($appointment_date, $duration)
    {
        $start = new DateTime($appointment_date);
        $end = clone $start;
        $end->modify("+$duration minutes");
        $start_str = $start->format("Y-m-d H:i:s");
        $end_str = $end->format("Y-m-d H:i:s");

        $stmt = $this->conn->prepare(
            "SELECT appointment_id FROM appointments 
             WHERE appointment_date < ? AND DATE_ADD(appointment_date, INTERVAL 
                 CASE service_id 
                     WHEN 1 THEN 60 
                     WHEN 2 THEN 90 
                     WHEN 3 THEN 120 
                     ELSE 60
                 END MINUTE) > ?"
        );
        $stmt->bind_param("ss", $end_str, $start_str);
        $stmt->execute();
        $result = $stmt->get_result();
        $conflict = $result->num_rows > 0;
        $stmt->close();
        return $conflict;
    }

    public function addAppointment($client_id, $service_id, $appointment_date)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO appointments (client_id, service_id, appointment_date, status) VALUES (?, ?, ?, 'Pending')"
        );
        $stmt->bind_param("iis", $client_id, $service_id, $appointment_date);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function updateAppointmentStatus($appointment_id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
        $id = (int)$appointment_id;
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected >= 0; // -1 means SQL error; 0 means no row matched (still ok)
    }

    public function deleteAppointment($appointment_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Testimonials
    public function addTestimonial($client_name, $message, $rating)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO testimonials (client_name, message, rating) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("ssi", $client_name, $message, $rating);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getApprovedTestimonials()
    {
        $result = $this->conn->query(
            "SELECT * FROM testimonials WHERE status = 'Approved' ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllTestimonials()
    {
        $result = $this->conn->query(
            "SELECT * FROM testimonials ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateTestimonialStatus($testimonial_id, $status)
    {
        $stmt = $this->conn->prepare(
            "UPDATE testimonials SET status = ? WHERE testimonial_id = ?"
        );
        $id = (int)$testimonial_id;
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected >= 0;
    }

    public function deleteTestimonial($testimonial_id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM testimonials WHERE testimonial_id = ?"
        );
        $stmt->bind_param("i", $testimonial_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Reports
    public function getRevenueStats()
    {
        $result = $this->conn->query(
            "SELECT
                COUNT(*) as total_appointments,
                SUM(CASE WHEN a.status = 'Completed' THEN s.price ELSE 0 END) as total_revenue,
                SUM(CASE WHEN a.status = 'Pending'   THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN a.status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed_count,
                SUM(CASE WHEN a.status = 'Completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN a.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_count
             FROM appointments a
             JOIN services s ON a.service_id = s.service_id"
        );
        return $result->fetch_assoc();
    }

    public function getMonthlyRevenue()
    {
        $result = $this->conn->query(
            "SELECT
                DATE_FORMAT(a.appointment_date, '%Y-%m') as month,
                DATE_FORMAT(a.appointment_date, '%b %Y') as month_label,
                COUNT(*) as appointment_count,
                SUM(s.price) as revenue
             FROM appointments a
             JOIN services s ON a.service_id = s.service_id
             WHERE a.status = 'Completed'
             GROUP BY DATE_FORMAT(a.appointment_date, '%Y-%m')
             ORDER BY month DESC
             LIMIT 6"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUpcomingAppointments()
    {
        $result = $this->conn->query(
            "SELECT a.appointment_id, c.first_name, c.last_name,
                    s.service_name, s.price, a.appointment_date, a.status
             FROM appointments a
             JOIN clients c ON a.client_id = c.client_id
             JOIN services s ON a.service_id = s.service_id
             WHERE a.appointment_date >= NOW() AND a.status IN ('Pending', 'Confirmed')
             ORDER BY a.appointment_date ASC
             LIMIT 10"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}
