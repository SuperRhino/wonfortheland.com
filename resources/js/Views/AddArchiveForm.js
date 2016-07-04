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
      author_name: null,
      author_twitter: null,
      author_url: null,
    };

    this.onSubmit = this.onSubmit.bind(this);
    this.onSuccess = this.onSuccess.bind(this);
  }

  componentWillMount() {
    let author = this.getAuthor();
    this.setState({
      author_name: author.name || null,
      author_twitter: author.twitter || null,
      author_url: author.url || null,
    })
  }

  getAuthor() {
    return {
      name: localStorage.getItem('author_name') || null,
      twitter: localStorage.getItem('author_twitter') || null,
      url: localStorage.getItem('author_url') || null,
    };
  }

  saveAuthor(author_name, author_twitter, author_url) {
    if (!! author_name) localStorage.setItem('author_name', author_name);
    if (!! author_twitter) localStorage.setItem('author_twitter', author_twitter);
    if (!! author_url) localStorage.setItem('author_url', author_url);
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
        {this.renderAuthorInputs()}
        <button type="submit" className="btn btn-primary btn-block btn-lg" {...buttonProps}>Submit</button>
      </form>
    );
  }

  renderAuthorInputs() {
    return (
      <div className="form-group form-group-sm">
        <label htmlFor="authorNameInput">Author</label>
        <div className="row">
          <div className="col-xs-6">
            <input
              id="authorNameInput"
              type="text"
              className="form-control input-lg"
              placeholder="Your Name"
              value={this.state.author_name}
              onChange={e => this.setState({author_name: e.target.value})}
            />
          </div>
          <div className="col-xs-6">
            <input
              id="authorTwitterInput"
              type="text"
              className="form-control input-lg"
              placeholder="@twitter"
              value={this.state.author_twitter}
              onChange={e => this.setState({author_twitter: e.target.value})}
            />
          </div>
        </div>
      </div>
    );
    // <div className="col-xs-4">
    //   <input
    //     id="authorUrlInput"
    //     type="text"
    //     className="form-control input-lg"
    //     placeholder="http://your.url.com/"
    //     value={this.state.author_url}
    //     onChange={e => this.setState({author_url: e.target.value})}
    //   />
    // </div>
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
      author_name: this.state.author_name,
      author_twitter: this.state.author_twitter,
      author_url: this.state.author_url,
    };

    console.log("archives:", data);
    ApiRequest.post('/archives')
      .data(data)
      .setAnonymous(true)
      .send(this.onSuccess);
  }

  onSuccess(res) {
    var author_name = this.state.author_name,
        author_twitter = this.state.author_twitter,
        author_url = this.state.author_url;

    this.saveAuthor(author_name, author_twitter, author_url);

    this.refs.archiveForm.reset();
    this.refs.srDropzone.cleanDropzone();
    this.setState({
      enabled: true,
      title: null,
      description: null,
      link_url: null,
      category: null,
      image: null,
      author_name,
      author_url,
    });

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