/*** IMPORTS ****************************************************************/

// Local dependencies
import { THEME_PREFIX } from '../../inc/constants';
import './styles/index.scss';

/*** COMPONENTS **************************************************************/

const ElipsisLoader = ({}) => {
	return (
		<span className={THEME_PREFIX + '-elipsis-loader'}>
			<span className="dot">&bull;</span>
			<span className="dot">&bull;</span>
			<span className="dot">&bull;</span>
		</span>
	);
};

/*** EXPORTS ****************************************************************/

export default ElipsisLoader;
