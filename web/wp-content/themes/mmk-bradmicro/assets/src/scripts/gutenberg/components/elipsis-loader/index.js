/*** IMPORTS ****************************************************************/

// Local dependencies
import "./styles/index.scss";

/*** COMPONENTS **************************************************************/

const ElipsisLoader = ({}) => {
	return (
		<span className={"madden-theme-elipsis-loader"}>
			<span className="dot">&bull;</span>
			<span className="dot">&bull;</span>
			<span className="dot">&bull;</span>
		</span>
	);
};

/*** EXPORTS ****************************************************************/

export default ElipsisLoader;
