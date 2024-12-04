<?php
// Fonction de vérification de l'unicité de l'email
function Verif_mail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT Email FROM USERS WHERE Email = :mail");
    $stmt->execute(['mail' => $email]);
    
    if ($stmt->rowCount() > 0) {
        // L'email existe déjà dans la base de données
        return false;
    } else {
        // L'email n'existe pas
        return true;
    }
}

// Fonction de vérification de l'age
function Verif_age($age) {
    $date = new DateTime($age);
    $now = new DateTime();
    $interval = $now->diff($date);
    $age = $interval->y;
    if ($age < 18) {
        return false;
        // L'utilisateur est mineur
    } else {
        return true;
        // L'utilisateur est majeur
    }
}

//Fonction de vérification du format des réponses 
function validateResponsesByRole($role, $responses) {
    $errors = [];

    // Règles de validation spécifiques à chaque champ
    $validationRules = [
        'Nom' => function($value) {
            return is_string($value) && preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/u', $value);
        },
        'Prenom' => function($value) {
            return is_string($value) && preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/u', $value);
        },
        'Date de naissance' => function($value) {
            $format = 'Y-m-d';
            $d = DateTime::createFromFormat($format, $value);
            return $d && $d->format($format) === $value;
        },
        'Sexe' => function($value) {
            return in_array($value, ['M', 'F']);
        },
        'Telephone' => function($value) {
            return preg_match('/^\d{10}$/', str_replace(' ', '', $value));
        },
        'Taille' => function($value) {
            return filter_var($value, FILTER_VALIDATE_INT) !== false && $value > 0;
        },
        'Poids' => function($value) {
            return filter_var($value, FILTER_VALIDATE_INT) !== false && $value > 0;
        },
        'Spécialité' => function($value) {
            return is_string($value) && !empty($value);
        },
        'Matricule' => function($value) {
            return preg_match('/^\d{11}$/', str_replace(' ', '', $value));
        },
        'Nom Entreprise' => function($value) {
            return is_string($value) && !empty($value);
        },
        'SIRET' => function($value) {
            return preg_match('/^\d{14}$/', str_replace(' ', '', $value)); // Un SIRET valide contient 14 chiffres
        }
    ];

    // Définition des champs spécifiques à chaque rôle
    $questionsByRole = [
        'patient' => ['Nom', 'Prenom', 'Date de naissance', 'Sexe', 'Telephone', 'Profil Picture', 'Taille', 'Poids', 'Traitements', 'Allergies', 'CNI'],
        'medecin' => ['Nom', 'Prenom', 'Spécialité', 'Telephone', 'Matricule', 'Profil Picture'],
        'entreprise' => ['Nom Entreprise', 'Telephone', 'Profil Picture', 'SIRET']
    ];

    // Obtenir les questions pour le rôle donné
    if (!isset($questionsByRole[$role])) {
        $errors[] = "Rôle inconnu : $role.";
        return $errors;
    }

    $questions = $questionsByRole[$role];

    // Valider les réponses
    foreach ($questions as $index => $question) {
        if (isset($responses[$index])) {
            $value = $responses[$index];
            if (isset($validationRules[$question]) && !$validationRules[$question]($value)) {
                $errors[] = "Le champ '$question' a une valeur invalide : $value.";
            }
        } else {
            if ($question === 'Profil Picture' || $question === 'CNI' || $question === 'Traitements' || $question === 'Allergies') {
                // Ces champs sont facultatifs
                continue;
            }
            $errors[] = "Le champ '$question' est manquant.";
        }
    }

    return $errors;
}

// Fonction pour vérifier si un mot de passe est haché
function isHashedPassword($passwordHash) {
    // Vérifie si le hachage est bien formaté (commence par $2y$ pour bcrypt)
    return (preg_match('/^\$2[ay]\$.{56}$/', $passwordHash) === 1);}


// Fonction pour ajouter un utilisateur dans la base de données
function addUser($pdo, $mdp, $email, $role) {
    isHashedPassword($mdp) || $mdp = password_hash($mdp, PASSWORD_DEFAULT); // Hachage du mot de passe si ce n'est

    try {
        $stmt = $pdo->prepare("INSERT INTO `USERS` (`Id_user`, `Passwd`, `Email`, `Role`) VALUES (NULL, :pswd, :email, :role)");
        $stmt->execute([
            'pswd' => password_hash($mdp, PASSWORD_DEFAULT), // Hachage du mot de passe pour la sécurité
            'email' => $email,
            'role' => $role
        ]);

        if ($stmt->rowCount() > 0) {
            return $pdo->lastInsertId(); // Retourne l'ID de l'utilisateur nouvellement inséré
        } else {
            return false; // Retourne false si l'insertion a échoué
        }
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return false; // Retourne false en cas d'erreur PDO
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
        return false; // Retourne false en cas d'erreur générale
    }
}

// Fonction pour ajouter un utilisateur selon son rôle dans la base de données
function addRole($pdo, $role, $newID, $reponses) {
    if ($role === "patient") {
        $stmt = $pdo->prepare("INSERT INTO `PATIENTS` (`Id_patient`, `Nom`, `Prenom`, `Date_naissance`, `Sexe`, `Telephone`, `Profile_picture`, `Taille`, `Poids`, `Traitements`, `Allergies`, `Cni`) 
        VALUES (:id, :nom, :prenom, :dateN, :Sexe, :Tel, :PP, :taille, :poids, :traitement, :allergie, :cni)");  
        $stmt->execute(['id' => $newID,
            'nom' => $reponses[0], 'prenom' => $reponses[1],'dateN' => $reponses[2], 'Sexe' => $reponses[3],
            'Tel' => str_replace(' ', '', $reponses[4]), 'PP' => $reponses[5],'taille' => str_replace(' ', '', $reponses[6]), 'poids' => str_replace(' ', '', $reponses[7]),
            'traitement' => $reponses[8], 'allergie' => $reponses[9],'cni' => $reponses[10]]);
        }

    elseif ($role=== "medecin") {
        $stmt = $pdo->prepare("INSERT INTO `MEDECINS` (`Id_medecin`, `Nom`, `Prenom`, `Specialite`, `Telephone`, `Matricule`, `Profile_picture`,`Statut_inscription`) 
        VALUES (:id, :nom, :prenom, :specialite, :Tel, :matricule, :PP, '0')");
        $stmt->execute(['id' => $newID,
            'nom' => $reponses[0], 'prenom' => $reponses[1],'specialite' => $reponses[2], 'Tel' => str_replace(' ', '', $reponses[3]),
            'matricule' => str_replace(' ', '', $reponses[4]), 'PP' => $reponses[5]]);
        }

    elseif ($role=== "entreprise") {
        $stmt = $pdo->prepare("INSERT INTO `ENTREPRISES` (`Id_entreprise`, `Nom_entreprise`, `Telephone`, `Profile_picture`, `Siret`, `Verif_inscription`) 
        VALUES (:id, :nom, :Tel, :PP, :siret, '0')");
        $stmt->execute(['id' => $newID,
            'nom' => $reponses[0], 'Tel' => str_replace(' ', '', $reponses[1]), 'PP' => $reponses[2], 'siret' => str_replace(' ', '', $reponses[3])])
        ;
    } 
    else {
        // Rôle inconnu
        header("Location: Form1_inscription.php#modal");
        exit();
    }

    //Vérifie si l'insert s'est bien déroulé
    if ($stmt->rowCount() > 0) {
        //insertion réussie
        return true ;
    } else {
        //insertion échouée
        return false;
    }
}

?>
