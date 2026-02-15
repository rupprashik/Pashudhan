package com.vetclinic.controller;

import com.vetclinic.service.AppointmentService;
import com.vetclinic.service.PatientService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("/api/analytics")
@CrossOrigin(origins = "${cors.allowed.origins}")
public class AnalyticsController {

    @Autowired
    private PatientService patientService;

    @Autowired
    private AppointmentService appointmentService;

    @GetMapping
    public ResponseEntity<Map<String, Object>> getAnalytics() {
        Map<String, Object> analytics = new HashMap<>();

        // Get counts
        Long totalPatients = patientService.countTotalPatients();
        Long todayAppointments = appointmentService.countTodayAppointments();

        analytics.put("totalPatients", totalPatients);
        analytics.put("todayAppointments", todayAppointments);
        analytics.put("upcomingAppointments", appointmentService.getUpcomingAppointments().size());

        return ResponseEntity.ok(analytics);
    }
}
