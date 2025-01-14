<?php
/**
 * Class Data Point Option.
 *
 * @package automattic/jetpack-mu-wpcom
 */

namespace Automattic\Jetpack\Jetpack_Mu_Wpcom\Global_Styles;

require_once __DIR__ . '/interface-data-point.php';

/**
 * Option Data Point.
 */
class Data_Point_Option implements Data_Point {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Option property to access, if exist.
	 *
	 * @var string
	 */
	private $option_property;

	/**
	 * Default option value.
	 *
	 * @var mixed
	 */
	private $default_value;

	/**
	 * Constructor.
	 *
	 * @param array $meta Data point description.
	 */
	public function __construct( $meta ) {
		if ( is_array( $meta['name'] ) ) {
			$this->option_name     = $meta['name'][0];
			$this->option_property = $meta['name'][1];
		} else {
			$this->option_name = $meta['name'];
		}

		if ( array_key_exists( 'default', $meta ) ) {
			$this->default_value = $meta['default'];
		} else {
			$this->default_value = false;
		}
	}

	/**
	 * Implements \Automattic\Jetpack\Jetpack_Mu_Wpcom\Global_Styles\Data_Point interface.
	 *
	 * @return mixed The value to return.
	 */
	public function get_value() {
		if ( ! isset( $this->option_property ) ) {
			return get_option( $this->option_name, $this->default_value );
		}

		$value = get_option( $this->option_name, array() );
		if ( is_array( $value ) && array_key_exists( $this->option_property, $value ) ) {
			return $value[ $this->option_property ];
		}

		return $this->default_value;
	}

	/**
	 * Return the option name this data point belongs to.
	 *
	 * @return string Option name
	 */
	public function get_option_name() {
		return $this->option_name;
	}

	/**
	 * Process new data.
	 *
	 * @param mixed $current_option_value Current option value.
	 * @param mixed $new_value Value to update.
	 * @return mixed The modified option value.
	 */
	public function process_data_point( $current_option_value, $new_value ) {
		$result = $current_option_value;

		if ( isset( $this->option_property ) ) {
			if ( is_array( $result ) ) {
				$result[ $this->option_property ] = $new_value;
			} else {
				// Unexpected current option value. We were expecting an array,
				// but it's something like a string or boolean and we can't
				// update the single data point.  Throw away the current option
				// value and make a new array.
				$result = array( $this->option_property => $new_value );
			}
		} else {
			$result = $new_value;
		}

		return $result;
	}
}
