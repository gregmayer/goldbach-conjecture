<?php

sequence( 4,100 );

class Prime {

    public static function isPrime( $candidate )
    {

        for( $g = 2; $g <= ( $candidate / 2 ); $g++ ) {

            if ( $candidate % $g == 0 ) {

                return false;

            }

        }

        return true;

    }

}

class PrimePool {

    protected $primes;
    protected $prime_pool_level;

    public function __construct()
    {

        $this->primes           = [];
        $this->prime_pool_level = 2;

    }

    public function seed( $max ) {

        /*
         * If our prime pool is empty or if the maximum value exceeds what we have available in the pool,
         * then we need to replenish the pool.
         */

        if ( empty( $this->primes ) || ( $this->prime_pool_level < $max ) ) {

            for( $g = $this->prime_pool_level; $g <= $max; $g++ ) {

                if ( Prime::isPrime( $g ) ) {

                    $this->primes[] = $g;

                }

            }

            $this->prime_pool_level = $max;

        }

        return $this->primes;

    }

}

class Goldbach
{

    protected $prime_pool;

    public function __construct( PrimePool $prime_pool )
    {

        $this->prime_pool = $prime_pool;

    }

    public function findPrimePairs( $candidate )
    {

        if ( ( !is_numeric( $candidate ) ) || ( $candidate % 2 !== 0 ) || ( $candidate < 4 ) ) {

            throw new Exception( 'Goldbach candidate must be an even integer greater than or equal to 4`.' );

        }

        $primes = $this->getPrimePool()->seed( $candidate );

        $sum   = 0;
        $point = 0;

        while( $sum != $candidate ) {

            $c = $candidate - $primes[$point];

            if ( in_array( $c, $primes ) ) {

                $sum = $primes[$point] + $c;

            } elseif( $point < ( count( $primes ) - 1 ) ) {

                $point++;

            } else {

                throw new Exception( 'Exhausted prime pool.' );

            }

        }

        return [ $primes[$point], $c ];

    }

    protected function getPrimePool()
    {

        return $this->prime_pool;

    }

}

function sequence( $start, $end )
{

    /*
     * We only want even numbers, so align with the nearest even number.  We then advance by two to stay with the
     * evens.
     */

    $sequence = $start + ( $start % 2 );

    $prime_pool = new PrimePool();

    $goldbach = new Goldbach( $prime_pool );

    while( $sequence <= $end ) {

        try {

            $numbers = $goldbach->findPrimePairs( $sequence );

        } catch( Exception $e ) {

            echo 'Unable to complete the sequence because we exhausted the prime pool.' . PHP_EOL;
            break;

        }

        echo sprintf( '%d = %d + %d', $sequence, $numbers[0], $numbers[1] ) . PHP_EOL;

        $sequence += 2;

    }

}
