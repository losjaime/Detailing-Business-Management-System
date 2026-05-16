<?php
require_once __DIR__ . '/../models/DataLayer.php';

class Controller
{
    private $dataLayer;

    public function __construct()
    {
        $this->dataLayer = new DataLayer();
    }

    public function handleRequest($action, $params = [])
    {
        switch ($action) {
            case 'index':
                return $this->index();
            case 'services':
                return $this->services();
            case 'book':
                return $this->book($params);
            case 'testimonials':
                return $this->testimonials($params);
            case 'admin':
                return $this->admin($params);
            default:
                return ['view' => 'index', 'data' => []];
        }
    }

    private function index()
    {
        return ['view' => 'index', 'data' => []];
    }

    private function services()
    {
        return ['view' => 'services', 'data' => ['services' => $this->dataLayer->getServices()]];
    }

    private function book($params)
    {
        $services = $this->dataLayer->getServices();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($params['book_appointment'])) {
            $required = ['name', 'email', 'phone', 'address', 'appointmentDate', 'appointmentTime', 'package'];
            foreach ($required as $field) {
                if (!isset($params[$field]) || empty(trim($params[$field]))) {
                    return ['view' => 'book', 'data' => ['services' => $services, 'error' => "Missing required field: $field"]];
                }
            }

            $name             = trim($params['name']);
            $email            = trim($params['email']);
            $phone            = trim($params['phone']);
            $address          = trim($params['address']);
            $appointment_date = trim($params['appointmentDate']);
            $appointment_time = trim($params['appointmentTime']);
            $package          = trim($params['package']);

            $name_parts = explode(" ", $name, 2);
            $first_name = $name_parts[0];
            $last_name  = $name_parts[1] ?? '';

            $appointment_datetime = DateTime::createFromFormat("Y-m-d H:i A", "$appointment_date $appointment_time");
            if (!$appointment_datetime) {
                return ['view' => 'book', 'data' => ['services' => $services, 'error' => 'Invalid date or time format']];
            }
            if ($appointment_datetime < new DateTime()) {
                return ['view' => 'book', 'data' => ['services' => $services, 'error' => 'Selected date/time is in the past']];
            }
            $formatted_datetime = $appointment_datetime->format("Y-m-d H:i:s");

            $service = $this->dataLayer->getServiceByName($package);
            if (!$service) {
                return ['view' => 'book', 'data' => ['services' => $services, 'error' => 'Invalid package selected']];
            }

            if ($this->dataLayer->checkAppointmentConflict($formatted_datetime, $service['duration'])) {
                return ['view' => 'book', 'data' => ['services' => $services, 'error' => 'Selected time slot is already booked. Please choose another time']];
            }

            $client = $this->dataLayer->getClientByEmail($email);
            if ($client) {
                $client_id = $client['client_id'];
            } else {
                $client_id = $this->dataLayer->addClient($first_name, $last_name, $email, $phone, $address);
                if (!$client_id) {
                    return ['view' => 'book', 'data' => ['services' => $services, 'error' => 'Failed to add client']];
                }
            }

            if ($this->dataLayer->addAppointment($client_id, $service['service_id'], $formatted_datetime)) {
                return ['view' => 'book', 'data' => ['services' => $services, 'success' => 'Appointment booked successfully!']];
            }
            return ['view' => 'book', 'data' => ['services' => $services, 'error' => 'Failed to book appointment']];
        }
        return ['view' => 'book', 'data' => ['services' => $services]];
    }

    private function testimonials($params)
    {
        $data = [
            'testimonials' => $this->dataLayer->getApprovedTestimonials(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($params['submit_testimonial'])) {
            $name    = trim($params['client_name'] ?? '');
            $message = trim($params['message'] ?? '');
            $rating  = (int)($params['rating'] ?? 0);

            if (empty($name) || empty($message) || $rating < 1 || $rating > 5) {
                $data['error'] = 'Please fill in all fields and select a star rating.';
            } elseif ($this->dataLayer->addTestimonial($name, $message, $rating)) {
                $data['success'] = 'Thank you! Your review has been submitted and is pending approval.';
            } else {
                $data['error'] = 'Something went wrong. Please try again.';
            }
        }

        return ['view' => 'testimonials', 'data' => $data];
    }

    private function admin($params)
    {
        $data = $this->fetchAdminData();
        $data['message'] = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($params['add_client'])) {
                $required = ['first_name', 'last_name', 'email', 'phone', 'address'];
                foreach ($required as $field) {
                    if (!isset($params[$field]) || empty(trim($params[$field]))) {
                        $data['message'] = "Error: Missing $field";
                        return ['view' => 'admin', 'data' => $data];
                    }
                }
                $data['message'] = $this->dataLayer->addClient(
                    $params['first_name'], $params['last_name'],
                    $params['email'], $params['phone'], $params['address']
                ) ? 'Client added successfully!' : 'Error adding client';

            } elseif (isset($params['update_client'])) {
                $data['message'] = $this->dataLayer->updateClientEmail($params['client_id'], $params['new_email'])
                    ? 'Client updated successfully!' : 'Error updating client';

            } elseif (isset($params['delete_client'])) {
                $data['message'] = $this->dataLayer->deleteClient($params['client_id'])
                    ? 'Client deleted successfully!' : 'Error deleting client';

            } elseif (isset($params['add_service'])) {
                $required = ['service_name', 'description', 'price', 'duration'];
                foreach ($required as $field) {
                    if (!isset($params[$field]) || empty(trim($params[$field]))) {
                        $data['message'] = "Error: Missing $field";
                        return ['view' => 'admin', 'data' => $data];
                    }
                }
                $data['message'] = $this->dataLayer->addService(
                    $params['service_name'], $params['description'],
                    $params['price'], $params['duration']
                ) ? 'Service added successfully!' : 'Error adding service';

            } elseif (isset($params['update_service'])) {
                $data['message'] = $this->dataLayer->updateService(
                    $params['service_id'], $params['new_price'], $params['new_duration']
                ) ? 'Service updated successfully!' : 'Error updating service';

            } elseif (isset($params['delete_service'])) {
                $data['message'] = $this->dataLayer->deleteService($params['service_id'])
                    ? 'Service deleted successfully!' : 'Error deleting service';

            } elseif (isset($params['update_appointment'])) {
                $data['message'] = $this->dataLayer->updateAppointmentStatus($params['appointment_id'], $params['new_status'])
                    ? 'Appointment updated successfully!' : 'Error updating appointment';

            } elseif (isset($params['delete_appointment'])) {
                $data['message'] = $this->dataLayer->deleteAppointment($params['appointment_id'])
                    ? 'Appointment deleted successfully!' : 'Error deleting appointment';

            } elseif (isset($params['update_testimonial'])) {
                $data['message'] = $this->dataLayer->updateTestimonialStatus($params['testimonial_id'], $params['new_status'])
                    ? 'Review updated successfully!' : 'Error updating review';

            } elseif (isset($params['delete_testimonial'])) {
                $data['message'] = $this->dataLayer->deleteTestimonial($params['testimonial_id'])
                    ? 'Review deleted successfully!' : 'Error deleting review';
            }

            $data = array_merge($data, $this->fetchAdminData());
        }

        return ['view' => 'admin', 'data' => $data];
    }

    private function fetchAdminData()
    {
        return [
            'clients'         => $this->dataLayer->getClients(),
            'services'        => $this->dataLayer->getServices(),
            'appointments'    => $this->dataLayer->getAppointments(),
            'testimonials'    => $this->dataLayer->getAllTestimonials(),
            'report'          => $this->dataLayer->getRevenueStats(),
            'monthly_revenue' => $this->dataLayer->getMonthlyRevenue(),
            'upcoming'        => $this->dataLayer->getUpcomingAppointments(),
        ];
    }
}
