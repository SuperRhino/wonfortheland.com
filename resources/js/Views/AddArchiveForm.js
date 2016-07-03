import React from 'react';
import Actions from '../Utils/Actions';
import ApiRequest from '../Api/ApiRequest';
import SRDropzone from '../Components/SRDropzone';
import Utils from '../Utils/Utils';

export default class AddArchiveForm extends React.Component {
  static propTypes = {};
  static defaultProps = {};

  constructor(props) {
    super(props);

    this.state = {
      enabled: true,
      title: null,
      description: null,
      link_url: null,
      category: null,
      image: null,
    };

    this.onSubmit = this.onSubmit.bind(this);
    this.onSuccess = this.onSuccess.bind(this);
  }

  render() {
    let buttonProps = this.state.enabled ? {} : {disabled: "disabled"};
    return (
      <form ref="archiveForm" role="form" onSubmit={this.onSubmit}>
        <div className="form-group">
          <label htmlFor="titleInput">Title</label>
          <input
            id="titleInput"
            type="text"
            className="form-control input-lg"
            placeholder="LeBron Space Dunk in Game 3"
            value={this.state.title}
            onChange={e => this.setState({title: e.target.value})}
          />
        </div>
        <div className="form-group">
          <label htmlFor="descInput">Description</label>
          <textarea
            id="descInput"
            className="form-control input-lg"
            rows="3"
            placeholder="LeBron gets a steal, stumbles on the fast-break, passes to Irving who lobs the handoff too high for a normal person but James throws it down with authority."
            value={this.state.description}
            onChange={e => this.setState({description: e.target.value})}
          ></textarea>
        </div>
        <div className="form-group">
          <label htmlFor="linkInput">Link / URL</label>
          <input
            id="linkInput"
            type="text"
            className="form-control input-lg"
            placeholder="http://www.nba.com/cavaliers/video/channel/gameday"
            value={this.state.link_url}
            onChange={e => this.setState({link_url: e.target.value})}
          />
        </div>
        {this.renderCategoryInput()}
        {this.renderImageInput()}
        <button type="submit" className="btn btn-primary btn-block btn-lg" {...buttonProps}>Submit</button>
      </form>
    );
  }

  renderImageInput() {
    return (
      <div className="form-group">
        <SRDropzone
          ref="srDropzone"
          style={styles.previewImage}
          activeStyle={styles.dragActive}
          multiple={false}
          onUploadStart={() => this.setState({enabled: false})}
          onUploadEnd={() => this.setState({enabled: true})}
          onUploadSuccess={image => this.setState({image})}
        />
      </div>
    );
  }

  renderCategoryInput() {
    let categories = ["Meme","Article","Media","Celebration","The Finals"];
    return (
      <div className="form-group">
        <label htmlFor="categoryInput">Category</label>
        <select
          id="categoryInput"
          className="form-control input-lg"
          value={this.state.category}
          onChange={e => this.setState({category: e.target.value})}>
          <option value="">— Choose Category —</option>
          {categories.map((category, index) => {
            return (<option key={'category-'+category} value={category}>{category}</option>);
          })}
        </select>
      </div>
    );
  }

  onSubmit(e) {
    e.preventDefault();
    if (! this.state.enabled) return;

    var data = {
      uri: Utils.cleanForUrl(this.state.title),
      title: this.state.title,
      description: this.state.description,
      category: this.state.category,
      link_url: this.state.link_url,
      image: this.state.image,
    };

    console.log("archives:", data);
    ApiRequest.post('/archives')
      .data(data)
      .setAnonymous(true)
      .send(this.onSuccess);
  }

  onSuccess(res) {
    this.refs.archiveForm.reset();
    this.refs.srDropzone.cleanDropzone();
    Utils.showSuccess("Thanks & Go CAVS!");
  }
}

var styles = {
  previewImage: {
    borderWidth: 1,
    borderStyle: "dashed",
  },
  dragActive: {
    borderColor: 'rgb(120,120,120)',
  },
};