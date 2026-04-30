<?php
// controllers/Controller.php
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
        $services = $this->dataLayer->getServices();
        return ['view' => 'services', 'data' => ['services' => $services]];
    }

    private function book($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($params['book_appointment'])) {
            // Validate required fields
            $required = ['name', 'email', 'phone', 'address', 'appointmentDate', 'appointmentTime', 'package'];
            foreach ($required as $field) {
                if (!isset($params[$field]) || empty(trim($params[$field]))) {
                    return ['view' => 'book', 'data' => ['error' => "Missing required field: $field"]];
                }
            }

            $name = trim($params['name']);
            $email = trim($params['email']);
            $phone = trim($params['phone']);
            $address = trim($params['address']);
            $appointment_date = trim($params['appointmentDate']);
            $appointment_time = trim($params['appointmentTime']);
            $package = trim($params['package']);

            // Split name
            $name_parts = explode(" ", $name, 2);
            $first_name = $name_parts[0];
            $last_name = $name_parts[1] ?? '';

            // Validate date/time
            $appointment_datetime = DateTime::createFromFormat("Y-m-d H:i A", "$appointment_date $appointment_time");
            if (!$appointment_datetime) {
                return ['view' => 'book', 'data' => ['error' => 'Invalid date or time format']];
            }
            if ($appointment_datetime < new DateTime()) {
                return ['view' => 'book', 'data' => ['error' => 'Selected date/time is in the past']];
            }
            $formatted_datetime = $appointment_datetime->format("Y-m-d H:i:s");

            // Validate package
            $service = $this->dataLayer->getServiceByName($package);
            if (!$service) {
                return ['view' => 'book', 'data' => ['error' => 'Invalid package selected']];
            }
            $duration = $service['duration'];

            // Check conflicts
            if ($this->dataLayer->checkAppointmentConflict($formatted_datetime, $duration)) {
                return ['view' => 'book', 'data' => ['error' => 'Selected time slot is already booked. Please choose another time']];
            }

            // Handle client
            $client = $this->dataLayer->getClientByEmail($email);
            if ($client) {
                $client_id = $client['client_id'];
            } else {
                $client_id = $this->dataLayer->addClient($first_name, $last_name, $email, $phone, $address);
                if (!$client_id) {
                    return ['view' => 'book', 'data' => ['error' => 'Failed to add client']];
                }
            }

            // Add appointment
            if ($this->dataLayer->addAppointment($client_id, $service['service_id'], $formatted_datetime)) {
                return ['view' => 'book', 'data' => ['success' => 'Appointment booked successfully!']];
            }
            return ['view' => 'book', 'data' => ['error' => 'Failed to book appointment']];
        }
        return ['view' => 'book', 'data' => []];
    }

    private function admin($params)
    {
        $data = [
            'clients' => $this->dataLayer->getClients(),
            'services' => $this->dataLayer->getServices(),
            'appointments' => $this->dataLayer->getAppointments(),
            'message' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($params['add_client'])) {
                $required = ['first_name', 'last_name', 'email', 'phone', 'address'];
                foreach ($required as $field) {
                    if (!isset($params[$field]) || empty(trim($params[$field]))) {
                        $data['message'] = "Error: Missing $field";
                        return ['view' => 'admin', 'data' => $data];
                    }
                }
                if ($this->dataLayer->addClient(
                    $params['first_name'],
                    $params['last_name'],
                    $params['email'],
                    $params['phone'],
                    $params['address']
                )) {
                    $data['message'] = 'Client added successfully!';
                } else {
                    $data['message'] = 'Error adding client';
                }
            } elseif (isset($params['update_client'])) {
                if ($this->dataLayer->updateClientEmail($params['client_id'], $params['new_email'])) {
                    $data['message'] = 'Client updated successfully!';
                } else {
                    $data['message'] = 'Error updating client';
                }
            } elseif (isset($params['delete_client'])) {
                if ($this->dataLayer->deleteClient($params['client_id'])) {
                    $data['message'] = 'Client deleted successfully!';
                } else {
                    $data['message'] = 'Error deleting client';
                }
            } elseif (isset($params['add_service'])) {
                $required = ['service_name', 'description', 'price', 'duration'];
                foreach ($required as $field) {
                    if (!isset($params[$field]) || empty(trim($params[$field]))) {
                        $data['message'] = "Error: Missing $field";
                        return ['view' => 'admin', 'data' => $data];
                    }
                }
                if ($this->dataLayer->addService(
                    $params['service_name'],
                    $params['description'],
                    $params['price'],
                    $params['duration']
                )) {
                    $data['message'] = 'Service added successfully!';
                } else {
                    $data['message'] = 'Error adding service';
                }
            } elseif (isset($params['update_service'])) {
                if ($this->dataLayer->updateService(
                    $params['service_id'],
                    $params['new_price'],
                    $params['new_duration']
                )) {
                    $data['message'] = 'Service updated successfully!';
                } else {
                    $data['message'] = 'Error updating service';
                }
            } elseif (isset($params['delete_service'])) {
                if ($this->dataLayer->deleteService($params['service_id'])) {
                    $data['message'] = 'Service deleted successfully!';
                } else {
                    $data['message'] = 'Error deleting service';
                }
            } elseif (isset($params['update_appointment'])) {
                if ($this->dataLayer->updateAppointmentStatus($params['appointment_id'], $params['new_status'])) {
                    $data['message'] = 'Appointment updated successfully!';
                } else {
                    $data['message'] = 'Error updating appointment';
                }
            } elseif (isset($params['delete_appointment'])) {
                if ($this->dataLayer->deleteAppointment($params['appointment_id'])) {
                    $data['message'] = 'Appointment deleted successfully!';
                } else {
                    $data['message'] = 'Error deleting appointment';
                }
            }
            // Refresh data
            $data['clients'] = $this->dataLayer->getClients();
            $data['services'] = $this->dataLayer->getServices();
            $data['appointments'] = $this->dataLayer->getAppointments();
        }
        return ['view' => 'admin', 'data' => $data];
    }
}