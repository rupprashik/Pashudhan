package com.vetclinic.service;

import com.vetclinic.model.Patient;
import com.vetclinic.repository.PatientRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

@Service
public class PatientService {

    @Autowired
    private PatientRepository patientRepository;

    // Get all patients
    public List<Patient> getAllPatients() {
        return patientRepository.findAll();
    }

    // Get patient by ID
    public Optional<Patient> getPatientById(Long id) {
        return patientRepository.findById(id);
    }

    // Create new patient
    public Patient createPatient(Patient patient) {
        return patientRepository.save(patient);
    }

    // Update patient
    public Patient updatePatient(Long id, Patient patientDetails) {
        Patient patient = patientRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Patient not found with id: " + id));

        patient.setAnimalName(patientDetails.getAnimalName());
        patient.setSpecies(patientDetails.getSpecies());
        patient.setBreed(patientDetails.getBreed());
        patient.setAge(patientDetails.getAge());
        patient.setGender(patientDetails.getGender());
        patient.setOwnerName(patientDetails.getOwnerName());
        patient.setOwnerPhone(patientDetails.getOwnerPhone());
        patient.setOwnerAddress(patientDetails.getOwnerAddress());
        patient.setMedicalHistory(patientDetails.getMedicalHistory());

        return patientRepository.save(patient);
    }

    // Delete patient
    public void deletePatient(Long id) {
        patientRepository.deleteById(id);
    }

    // Search patients
    public List<Patient> searchPatients(String searchTerm) {
        return patientRepository.searchPatients(searchTerm);
    }

    // Get patients by species
    public List<Patient> getPatientsBySpecies(String species) {
        return patientRepository.findBySpecies(species);
    }

    // Count total patients
    public Long countTotalPatients() {
        return patientRepository.countTotalPatients();
    }
}
