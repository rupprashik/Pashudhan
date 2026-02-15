package com.vetclinic.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import java.time.LocalDateTime;

@Entity
@Table(name = "patients")
public class Patient {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @NotBlank(message = "Animal name is required")
    @Column(nullable = false)
    private String animalName;

    @NotBlank(message = "Species is required")
    private String species; // Dog, Cat, Cow, Goat, etc.

    private String breed;

    private Integer age; // in months

    private String gender; // Male/Female

    @NotBlank(message = "Owner name is required")
    @Column(nullable = false)
    private String ownerName;

    @Pattern(regexp = "^[0-9]{10}$", message = "Phone number must be 10 digits")
    private String ownerPhone;

    private String ownerAddress;

    @Column(length = 500)
    private String medicalHistory;

    @Column(updatable = false)
    private LocalDateTime registeredDate;

    private LocalDateTime lastVisit;

    // Constructors
    public Patient() {
    }

    public Patient(String animalName, String species, String ownerName, String ownerPhone) {
        this.animalName = animalName;
        this.species = species;
        this.ownerName = ownerName;
        this.ownerPhone = ownerPhone;
    }

    // Lifecycle callbacks
    @PrePersist
    protected void onCreate() {
        registeredDate = LocalDateTime.now();
        lastVisit = LocalDateTime.now();
    }

    @PreUpdate
    protected void onUpdate() {
        lastVisit = LocalDateTime.now();
    }

    // Getters and Setters
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getAnimalName() {
        return animalName;
    }

    public void setAnimalName(String animalName) {
        this.animalName = animalName;
    }

    public String getSpecies() {
        return species;
    }

    public void setSpecies(String species) {
        this.species = species;
    }

    public String getBreed() {
        return breed;
    }

    public void setBreed(String breed) {
        this.breed = breed;
    }

    public Integer getAge() {
        return age;
    }

    public void setAge(Integer age) {
        this.age = age;
    }

    public String getGender() {
        return gender;
    }

    public void setGender(String gender) {
        this.gender = gender;
    }

    public String getOwnerName() {
        return ownerName;
    }

    public void setOwnerName(String ownerName) {
        this.ownerName = ownerName;
    }

    public String getOwnerPhone() {
        return ownerPhone;
    }

    public void setOwnerPhone(String ownerPhone) {
        this.ownerPhone = ownerPhone;
    }

    public String getOwnerAddress() {
        return ownerAddress;
    }

    public void setOwnerAddress(String ownerAddress) {
        this.ownerAddress = ownerAddress;
    }

    public String getMedicalHistory() {
        return medicalHistory;
    }

    public void setMedicalHistory(String medicalHistory) {
        this.medicalHistory = medicalHistory;
    }

    public LocalDateTime getRegisteredDate() {
        return registeredDate;
    }

    public void setRegisteredDate(LocalDateTime registeredDate) {
        this.registeredDate = registeredDate;
    }

    public LocalDateTime getLastVisit() {
        return lastVisit;
    }

    public void setLastVisit(LocalDateTime lastVisit) {
        this.lastVisit = lastVisit;
    }
}
