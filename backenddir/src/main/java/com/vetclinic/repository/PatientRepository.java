package com.vetclinic.repository;

import com.vetclinic.model.Patient;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public interface PatientRepository extends JpaRepository<Patient, Long> {

    // Find by owner phone
    List<Patient> findByOwnerPhone(String ownerPhone);

    // Search by animal name or owner name
    @Query("SELECT p FROM Patient p WHERE " +
           "LOWER(p.animalName) LIKE LOWER(CONCAT('%', :searchTerm, '%')) OR " +
           "LOWER(p.ownerName) LIKE LOWER(CONCAT('%', :searchTerm, '%'))")
    List<Patient> searchPatients(@Param("searchTerm") String searchTerm);

    // Find by species
    List<Patient> findBySpecies(String species);

    // Count total patients
    @Query("SELECT COUNT(p) FROM Patient p")
    Long countTotalPatients();
}
