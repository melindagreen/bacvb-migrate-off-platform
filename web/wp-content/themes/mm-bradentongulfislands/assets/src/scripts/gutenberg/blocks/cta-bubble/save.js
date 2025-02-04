import { RichText, useBlockProps, InnerBlocks } from "@wordpress/block-editor";

const Save = (props) => {
  const { attributes: { title, description, ctaText}, className } = props;
  const blockProps = useBlockProps.save();

  return (
    <section className={className}>
          <div className="close" aria-label="close">Close X</div>
          {<RichText.Content { ...blockProps } className="contents-title" tagName="h2" value={ title } />}
          {<RichText.Content { ...blockProps } className="contents-description" tagName="p" value={ description } />}
          <RichText.Content { ...blockProps } className="contents-ctatext" tagName="button" value={ ctaText } />
    </section>
  )
}

/*** EXPORTS ****************************************************************/
export default Save;