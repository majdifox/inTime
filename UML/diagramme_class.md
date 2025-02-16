```mermaid
classDiagram
    class User {
        <<abstract>>
        +int id
        +string firstName
        +string lastName
        +string email
        +string phone
        +Date birthday
        +string profilePicture
        +string password
        +string role
    }

    class Rider {
        // Inherits from User
    }

    class Driver {
        +string driverLicense
        +string CNIE
        +string insuranceProof
        +string screeningStatus
        +boolean available
    }

    User <|-- Rider
    User <|-- Driver

    class Vehicle {
        +int id
        +string make
        +string model
        +int year
        +string category
    }
    Driver "1" --> "1" Vehicle : drives

    class Ride {
        +int id
        +string pickupLocation
        +string dropoffLocation
        +DateTime scheduledTime
        +float price
        +string status
    }
    Rider "1" -- "*" Ride : requests
    Driver "1" -- "*" Ride : assigned to
    Ride --> "1" Vehicle : uses

    class ChatMessage {
<!-- [MermaidChart: 7ff5fb3a-fb14-4f7c-9238-1f89450320d9] -->
        +int id
        +string message
        +DateTime timestamp
    }
    User "1" -- "*" ChatMessage : sends/receives

    class Notification {
        +int id
        +string type
        +string message
        +boolean readStatus
        +DateTime timestamp
    }
    User "1" -- "*" Notification : receives

    class Feedback {
        +int id
        +int rating
        +string comments
        +DateTime timestamp
    }
    Ride "1" -- "1..*" Feedback : has
    User "1" -- "*" Feedback : gives
