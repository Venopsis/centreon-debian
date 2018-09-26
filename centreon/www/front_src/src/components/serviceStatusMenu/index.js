import React, { Component } from "react";
import PropTypes from 'prop-types';
import numeral from "numeral";
import { Link } from "react-router-dom";

class ServiceStatusMenu extends Component {
  constructor(props) {
    super(props);

    this.setWrapperRef = this.setWrapperRef.bind(this);
    this.handleClickOutside = this.handleClickOutside.bind(this);
  }


  state = {
    toggled: false
  };

  toggle = () => {
    const { toggled } = this.state;
    this.setState({
      toggled: !toggled
    });

  };

  ///outside click

  componentDidMount() {
    document.addEventListener('mousedown', this.handleClickOutside);
  }

  componentWillUnmount() {
    document.removeEventListener('mousedown', this.handleClickOutside);
  }

  /**
   * Set the wrapper ref
   */
  setWrapperRef(node) {
    this.wrapperRef = node;
  }

  /**
   * Alert if clicked on outside of element
   */
  handleClickOutside(event) {
    if (this.wrapperRef && !this.wrapperRef.contains(event.target)) {
      this.setState({
        toggled: false
      });
    }
  }
  ////end outside click
 

  render() {
    const { data } = this.props;

    if (!data || !data.total) {
      return null;
    }

    const { critical, ok, pending, total, unknown, warning } = data;

    const { toggled } = this.state;

    return (
      <div  class={"wrap-right-services" + (toggled ? " submenu-active" : "")}>
        <span class="wrap-right-icon" onClick={this.toggle.bind(this)}>
          <span class="iconmoon icon-services">
            {pending > 0 ? <span class="custom-icon" /> : null}
          </span>
          <span class="wrap-right-icon__name">Services</span>
        </span>
        <Link to="./main.php?p=20201&o=svc_critical&search=" class={"wrap-middle-icon round round-small " + (critical.unhandled > 0 ? "red" : "red-bordered")} >
          <a class="number">
            <span>{numeral(critical.unhandled).format("0a")}</span>
          </a>
        </Link>
        <Link to="./main.php?p=20201&o=svc_warning&search=" class={"wrap-middle-icon round round-small " + (warning.unhandled > 0 ? "orange" : "orange-bordered")}>
          <a class="number">
            <span>{numeral(warning.unhandled).format("0a")}</span>
          </a>
        </Link>
        <Link to="./main.php?p=20201&o=svc_unknown&search=" class={"wrap-middle-icon round round-small " + (unknown.unhandled > 0 ? "gray-light" : "gray-light-bordered")}>
          <a class="number">
            <span>{numeral(unknown.unhandled).format("0a")}</span>
          </a>
        </Link>
        <Link to="./main.php?p=20201&o=svc_ok&search=" class={"wrap-middle-icon round round-small " + (ok > 0 ? "green" : "green-bordered")}>
          <a class="number">
            <span>{numeral(ok).format("0a")}</span>
          </a>
        </Link>
        <Link to="./main.php?p=20201&o=svc_pending&search=" class={"wrap-middle-icon round round-small " + (pending > 0 ? "blue" : "blue-bordered")}>
          <a class="number">
            <span>{numeral(pending).format("0a")}</span>
          </a>
        </Link>
        <span ref={this.setWrapperRef} class="toggle-submenu-arrow" onClick={this.toggle.bind(this)} >{this.props.children}</span>
        <div class="submenu services">
          <div class="submenu-inner">
            <ul class="submenu-items list-unstyled">
              <li class="submenu-item">
                <Link
                  to={"./main.php?p=20201&o=svc&search="}
                  class="submenu-item-link"
                >
                  <span>All services:</span>
                  <span class="submenu-count">{total}</span>
                </Link>
              </li>
              <li class="submenu-item">
                <Link
                  to={"./main.php?p=20201&o=svc_critical&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored red">Critical services:</span>
                  <span class="submenu-count">
                    {critical.unhandled}/{critical.total}
                  </span>
                </Link>
              </li>
              <li class="submenu-item">
                <Link
                  to={"./main.php?p=20201&o=svc_warning&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored orange">Warning services:</span>
                  <span class="submenu-count">
                    {warning.unhandled}/{warning.total}
                  </span>
                </Link>
              </li>
              <li class="submenu-item">
                <Link
                  to={"./main.php?p=20201&o=svc_unknown&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored gray">Unknown services:</span>
                  <span class="submenu-count">
                    {unknown.unhandled}/{unknown.total}
                  </span>
                </Link>
              </li>
              <li class="submenu-item">
                <Link
                  to={"./main.php?p=20201&o=svc_ok&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored green">Ok services:</span>
                  <span class="submenu-count">{ok}</span>
                </Link>
              </li>
              <li class="submenu-item">
                <Link
                  to={"./main.php?p=20201&o=svc_pending&search="}
                  class="submenu-item-link"
                >
                  <span class="dot-colored blue">Pending services:</span>
                  <span class="submenu-count">{pending}</span>
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    );
  }
}



export default ServiceStatusMenu;

ServiceStatusMenu.propTypes = {
  children: PropTypes.element.isRequired,
};

