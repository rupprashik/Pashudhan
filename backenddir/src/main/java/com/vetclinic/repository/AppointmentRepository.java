package com.vetclinic.repository;

import com.vetclinic.model.Appointment;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.stereotype.Repository;

import java.time.LocalDateTime;
import java.util.List;

@Repository
public interface AppointmentRepository extends JpaRepository<Appointment, Long> {

    // Find appointments by patient ID
    List<Appointment> findByPatientId(Long patientId);

    // Find appointments by status
    List<Appointment> findByStatus(String status);

    // Find appointments between dates
    List<Appointment> findByAppointmentDateBetween(LocalDateTime start, LocalDateTime end);

    // Count today's appointments
    @Query("SELECT COUNT(a) FROM Appointment a WHERE " +
           "CAST(a.appointmentDate AS date) = CURRENT_DATE")
    Long countTodayAppointments();

    // Find upcoming appointments
    @Query("SELECT a FROM Appointment a WHERE " +
           "a.appointmentDate > CURRENT_TIMESTAMP AND a.status = 'SCHEDULED' " +
           "ORDER BY a.appointmentDate ASC")
    List<Appointment> findUpcomingAppointments();
}