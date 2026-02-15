package com.vetclinic;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

@SpringBootApplication
public class VetClinicApplication {

    public static void main(String[] args) {
        SpringApplication.run(VetClinicApplication.class, args);
        System.out.println("🐾 Veterinary Clinic Management System Started!");
        System.out.println("📍 Access at: http://localhost:8080");
        System.out.println("💾 H2 Console: http://localhost:8080/h2-console");
    }
}
