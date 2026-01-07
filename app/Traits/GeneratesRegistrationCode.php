<?php

namespace App\Traits;

trait GeneratesRegistrationCode
{
    /**
     * Genera un código de inscripción con formato: [1-7][4 dígitos][letra verificación]
     * Ejemplo: 34354F, 62935I, 21038B
     * 
     * @param int $sequenceNumber Número secuencial (generalmente el ID del registro)
     * @return string Código generado
     */
    public static function generateRegistrationCode(int $sequenceNumber): string
    {
        // Primer dígito aleatorio entre 1 y 7
        $firstDigit = rand(1, 7);
        
        // Formatear el número secuencial a 4 dígitos con ceros a la izquierda
        $sequencePart = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        
        // Combinar primer dígito + secuencia
        $baseCode = $firstDigit . $sequencePart;
        
        // Calcular letra de verificación
        $verificationLetter = self::calculateVerificationLetter($baseCode);
        
        // Código final
        return $baseCode . $verificationLetter;
    }
    
    /**
     * Calcula la letra de verificación basada en el algoritmo:
     * Suma de cada dígito multiplicado por su posición (1-indexed)
     * El resultado módulo 11 + 65 da el código ASCII de la letra
     * 
     * @param string $baseCode Código base (solo números)
     * @return string Letra de verificación (A-K)
     */
    protected static function calculateVerificationLetter(string $baseCode): string
    {
        $sum = 0;
        $length = strlen($baseCode);
        
        for ($i = 0; $i < $length; $i++) {
            $digit = (int) substr($baseCode, $i, 1);
            $position = $i + 1; // Posición 1-indexed
            $sum += $digit * $position;
        }
        
        // Módulo 11 + 65 (ASCII de 'A')
        $letterCode = ($sum % 11) + 65;
        
        return chr($letterCode);
    }
    
    /**
     * Valida si un código de inscripción es válido
     * 
     * @param string $code Código a validar
     * @return bool True si el código es válido
     */
    public static function validateRegistrationCode(string $code): bool
    {
        // Verificar longitud (debe ser 6 caracteres: 5 dígitos + 1 letra)
        if (strlen($code) !== 6) {
            return false;
        }
        
        // Extraer base y letra
        $baseCode = substr($code, 0, 5);
        $providedLetter = substr($code, 5, 1);
        
        // Verificar que la base sean solo números
        if (!ctype_digit($baseCode)) {
            return false;
        }
        
        // Verificar que la letra sea una letra
        if (!ctype_alpha($providedLetter)) {
            return false;
        }
        
        // Calcular letra esperada
        $expectedLetter = self::calculateVerificationLetter($baseCode);
        
        // Comparar
        return strtoupper($providedLetter) === $expectedLetter;
    }
}
